<?php

namespace Tests\Feature\Admin;

use App\Actions\Settings\InitializeWebsiteAddresses;
use App\Actions\Settings\InitializeWebsiteContacts;
use App\Models\User;
use App\Models\WebsiteAddress;
use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\AddressValue;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WebsiteAddressesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_SETTINGS_MANAGE]);
        $this->actingAs($user);
    }

    private function email(string $value = 'office@example.test', array $extra = []): array
    {
        return array_replace(['value' => $value, 'label' => 'Office', 'is_active' => 1, 'is_primary' => 0], $extra);
    }

    private function phone(string $value = '+44 20 1234 5678', array $extra = []): array
    {
        return array_replace(['value' => $value, 'label' => 'Office', 'subtype' => 'landline', 'is_active' => 1, 'is_primary' => 0], $extra);
    }

    private function address(array $extra = [], array $emails = [], array $phones = []): array
    {
        return array_replace(['label' => 'Head Office', 'address_line_1' => "10 Example Road\nThird Floor", 'city' => 'London',
            'postal_code' => '00120', 'country' => 'United Kingdom', 'is_active' => 1, 'is_primary' => 0,
            'contacts_present' => 1, 'contacts_ready' => 1, 'emails_count' => count($emails), 'phones_count' => count($phones),
            'emails' => $emails, 'phones' => $phones], $extra);
    }

    private function payload(array $addresses = [], array $emails = [], array $phones = [], array $extra = []): array
    {
        return array_replace(['site_name' => 'Company', 'contacts_version' => WebsiteSetting::singleton()->contacts_version ?? 0,
            'contacts_present' => 1, 'contacts_ready' => 1, 'emails_count' => count($emails), 'phones_count' => count($phones), 'emails' => $emails, 'phones' => $phones,
            'addresses_present' => 1, 'addresses_ready' => 1, 'addresses_count' => count($addresses), 'removed_addresses_count' => 0, 'removed_addresses' => [],
            'addresses' => $addresses, 'collections_complete' => 1], $extra);
    }

    private function save(array $payload)
    {
        return $this->patch(route('admin.settings.update'), $payload);
    }

    private function persisted(WebsiteAddress $address): array
    {
        $emails = [];
        $phones = [];
        foreach ($address->contacts as $contact) {
            $row = $contact->only(['id', 'label', 'value', 'is_primary', 'is_active']);
            if ($contact->type === 'phone') {
                $row['subtype'] = $contact->subtype;
                $phones[] = $row;
            } else {
                $emails[] = $row;
            }
        }

        return $this->address($address->only(['id', ...AddressValue::FIELDS, 'is_primary', 'is_active']), $emails, $phones);
    }

    public function test_addresses_and_new_scoped_contacts_save_together_without_replacing_defaults(): void
    {
        $same = $this->email('same@example.test', ['is_primary' => 1]);
        $rows = [$this->address(['is_primary' => 1], [$same], [$this->phone('+12025550100', ['is_primary' => 1])]),
            $this->address(['label' => '東京事務所', 'address_line_1' => '東京都千代田区 1-2-3', 'city' => '東京', 'country' => '日本'], [$same])];
        $this->save($this->payload($rows, [$same]))->assertSessionHasNoErrors();
        $owner = WebsiteSetting::singleton();
        $this->assertDatabaseCount('website_addresses', 2);
        $this->assertDatabaseCount('website_contacts', 4);
        $this->assertNull($owner->primaryEmail()->website_address_id);
        $this->assertSame('Head Office', $owner->primaryAddress()->label);
        foreach ($owner->activeAddresses() as $address) {
            $this->assertSame('same@example.test', $address->primaryEmail()->value);
            $this->assertSame($address->id, $address->primaryEmail()->website_address_id);
        }
        $this->assertSame(1, $owner->contacts_version);
        $this->assertSame('00120', $owner->addresses->first()->postal_code);
    }

    public function test_address_edit_order_and_primary_switch_preserve_ids_and_contact_scopes(): void
    {
        $this->save($this->payload([$this->address(['is_primary' => 1], [$this->email()]), $this->address(['label' => 'Workshop'])], [$this->email('default@example.test')]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->addresses;
        $contactId = $rows[0]->contacts->first()->id;
        $first = $this->persisted($rows[0]);
        $first['is_primary'] = 0;
        $second = $this->persisted($rows[1]);
        $second['is_primary'] = 1;
        $second['city'] = 'Paris';
        $this->save($this->payload([$second, $first], [$this->email('default@example.test', ['id' => WebsiteSetting::singleton()->contacts->first()->id])]))->assertSessionHasNoErrors();
        $owner = WebsiteSetting::singleton();
        $this->assertSame([$rows[1]->id, $rows[0]->id], $owner->addresses->pluck('id')->all());
        $this->assertSame($rows[1]->id, $owner->primaryAddress()->id);
        $this->assertSame($contactId, $rows[0]->fresh()->contacts->first()->id);
        $this->assertNull($owner->primaryEmail());
    }

    public function test_required_bounded_text_unicode_and_control_validation(): void
    {
        foreach ([['label' => ''], ['label' => str_repeat('x', 121)], ['address_line_1' => ''], ['address_line_1' => str_repeat('x', 2001)],
            ['city' => str_repeat('x', 121)], ['postal_code' => str_repeat('0', 33)], ['country' => "GB\t"], ['address_line_1' => "Road\0Name"]] as $invalid) {
            $this->save($this->payload([$this->address($invalid)]))->assertSessionHasErrors();
        }
        $this->assertDatabaseCount('website_addresses', 0);
        $this->save($this->payload([$this->address(['label' => 'Siège social', 'address_line_1' => "Étage 2\r\n10 Rue Exemple", 'city' => '', 'country' => ''])]))->assertSessionHasNoErrors();
        $this->assertSame("Étage 2\r\n10 Rue Exemple", WebsiteAddress::first()->address_line_1);
    }

    public function test_duplicate_labeled_locations_are_rejected_but_distinct_labels_may_share_an_address(): void
    {
        $this->save($this->payload([$this->address(), $this->address(['label' => 'HEAD OFFICE'])]))->assertSessionHasErrors('addresses.1.label');
        $this->save($this->payload([$this->address(), $this->address(['label' => 'Registered Office'])]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('website_addresses', 2);
    }

    public function test_primary_address_must_be_unique_and_active_and_has_no_implicit_replacement(): void
    {
        $this->save($this->payload([$this->address(['is_primary' => 1, 'is_active' => 0])]))->assertSessionHasErrors('addresses.0.is_primary');
        $this->save($this->payload([$this->address(['is_primary' => 1]), $this->address(['label' => 'Other', 'is_primary' => 1])]))->assertSessionHasErrors('addresses');
        $this->save($this->payload([$this->address(['is_primary' => 1]), $this->address(['label' => 'Other'])]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->addresses;
        $first = $this->persisted($rows[0]);
        $first['is_primary'] = 0;
        $first['is_active'] = 0;
        $this->save($this->payload([$first, $this->persisted($rows[1])]))->assertSessionHasNoErrors();
        $this->assertNull(WebsiteSetting::singleton()->primaryAddress());
        $this->save($this->payload([$this->persisted($rows[1])]))->assertSessionHasNoErrors();
        $this->assertNull(WebsiteSetting::singleton()->primaryAddress());
    }

    public function test_default_and_each_address_primary_and_duplicate_rules_are_independent(): void
    {
        $primary = $this->email('shared@example.test', ['is_primary' => 1]);
        $this->save($this->payload([$this->address([], [$primary, $this->email('SHARED@example.test')])]))->assertSessionHasErrors('addresses.0.emails.1.value');
        $this->save($this->payload([$this->address([], [$primary, $this->email('two@example.test', ['is_primary' => 1])])]))->assertSessionHasErrors('addresses.0.emails');
        $this->save($this->payload([$this->address([], [$this->email('x@example.test', ['is_primary' => 1, 'is_active' => 0])])]))->assertSessionHasErrors('addresses.0.emails.0.is_primary');
        $this->save($this->payload([$this->address([], [$primary]), $this->address(['label' => 'Other'], [$primary])], [$primary]))->assertSessionHasNoErrors();
        $this->assertSame(3, WebsiteContact::where('is_primary', true)->count());
    }

    public function test_inactive_location_hides_contacts_and_reactivation_restores_individual_eligibility(): void
    {
        $this->save($this->payload([$this->address([], [$this->email(), $this->email('hidden@example.test', ['is_active' => 0])], [$this->phone()])]))->assertSessionHasNoErrors();
        $address = WebsiteAddress::first();
        $row = $this->persisted($address);
        $row['is_active'] = 0;
        $this->save($this->payload([$row]))->assertSessionHasNoErrors();
        $this->assertCount(0, $address->fresh()->activeEmailContacts());
        $this->assertNull($address->contacts->first()->fresh()->mailtoUrl());
        $this->assertNull($address->contacts->last()->fresh()->telUrl());
        $this->assertDatabaseCount('website_contacts', 3);
        $row['is_active'] = 1;
        $this->save($this->payload([$row]))->assertSessionHasNoErrors();
        $this->assertCount(1, $address->fresh()->activeEmailContacts());
        $this->assertSame('mailto:office@example.test', $address->fresh()->activeEmailContacts()->first()->mailtoUrl());
    }

    public function test_cross_record_address_and_cross_scope_contact_ids_are_rejected_atomically(): void
    {
        $this->save($this->payload([$this->address([], [$this->email()]), $this->address(['label' => 'Other'], [$this->email('other@example.test')])]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->addresses;
        $other = WebsiteSetting::forceCreate(['id' => 500, 'site_name' => 'Other settings']);
        $foreign = WebsiteAddress::forceCreate(['website_setting_id' => $other->id, 'label' => 'Foreign', 'address_line_1' => 'Other', 'normalized_hash' => str_repeat('a', 64), 'sort_order' => 1]);
        $this->save($this->payload([$this->address(['id' => $foreign->id])], [], [], ['site_name' => 'Must not persist']))->assertSessionHasErrors('addresses.0.id');
        $first = $this->persisted($rows[0]);
        $first['emails'][0]['id'] = $rows[1]->contacts->first()->id;
        $this->save($this->payload([$first, $this->persisted($rows[1])], [], [], ['site_name' => 'Must not persist']))->assertSessionHasErrors('addresses.0.emails.0.id');
        $this->assertSame('Company', WebsiteSetting::singleton()->site_name);
        $this->assertDatabaseCount('website_contacts', 2);
    }

    public function test_address_removal_requires_explicit_exact_child_removal_and_preserves_default_contacts(): void
    {
        $this->save($this->payload([$this->address([], [$this->email()])], [$this->email('default@example.test')]))->assertSessionHasNoErrors();
        $address = WebsiteAddress::first();
        $default = WebsiteSetting::singleton()->contacts->first();
        $this->save($this->payload([], [$this->email($default->value, ['id' => $default->id])]))->assertSessionHasErrors('addresses');
        $this->save($this->payload([], [$this->email($default->value, ['id' => $default->id])], [], [
            'removed_addresses_count' => 1, 'removed_addresses' => [['id' => $address->id, 'contact_ids' => [$default->id]]],
        ]))->assertSessionHasErrors('addresses');
        $this->save($this->payload([], [$this->email($default->value, ['id' => $default->id])], [], [
            'removed_addresses_count' => 1, 'removed_addresses' => [['id' => $address->id, 'contact_ids' => $address->contacts->pluck('id')->all()]],
        ]))->assertSessionHasNoErrors();
        $this->assertDatabaseCount('website_addresses', 0);
        $this->assertDatabaseCount('website_contacts', 1);
        $this->assertNotNull($default->fresh());
    }

    public function test_partial_collection_updates_preserve_unsubmitted_scopes(): void
    {
        $this->save($this->payload([$this->address([], [$this->email()])], [$this->email('default@example.test')]))->assertSessionHasNoErrors();
        $address = WebsiteAddress::first();
        $contact = $address->contacts->first();
        $row = $address->only(['id', ...AddressValue::FIELDS, 'is_active', 'is_primary']);
        $row['city'] = 'Paris';
        $payload = $this->payload([$row]);
        foreach (['contacts_present', 'contacts_ready', 'emails_count', 'phones_count', 'emails', 'phones'] as $key) {
            unset($payload[$key]);
        }
        $this->save($payload)->assertSessionHasNoErrors();
        $this->assertSame('Paris', $address->fresh()->city);
        $this->assertSame($contact->value, $contact->fresh()->value);
        $this->assertDatabaseCount('website_contacts', 2);
        $this->save(['site_name' => 'General only'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('website_addresses', 1);
        $this->assertDatabaseCount('website_contacts', 2);
    }

    public function test_truncated_counts_missing_terminal_and_unhydrated_nested_editor_never_delete_data(): void
    {
        $this->save($this->payload([$this->address([], [$this->email()])]))->assertSessionHasNoErrors();
        $row = $this->persisted(WebsiteAddress::first());
        $this->save(['site_name' => 'Truncated', 'collections_expected' => 1])->assertSessionHasErrors('collections_complete');
        foreach ([['collections_complete' => 0], ['addresses_count' => 2], ['addresses_ready' => 0], ['emails_count' => 1]] as $extra) {
            $this->save($this->payload([$row], [], [], $extra))->assertSessionHasErrors();
        }
        foreach (['contacts_ready' => 0, 'emails_count' => 2, 'contacts_present' => null] as $key => $value) {
            $bad = $row;
            $bad[$key] = $value;
            $this->save($this->payload([$bad]))->assertSessionHasErrors();
        }
        $bad = $row;
        $bad['emails'] = [];
        $this->save($this->payload([$bad]))->assertSessionHasErrors('addresses.0.emails_count');
        $this->assertDatabaseCount('website_contacts', 1);
        $this->assertDatabaseCount('website_addresses', 1);
    }

    public function test_nested_limits_unknown_metadata_and_address_count_are_validated(): void
    {
        $emails = [];
        for ($i = 0; $i < 11; $i++) {
            $emails[] = $this->email('a'.$i.'@example.test');
        }
        $this->save($this->payload([$this->address([], $emails)]))->assertSessionHasErrors('addresses.0.emails');
        $rows = [];
        for ($i = 0; $i < 51; $i++) {
            $rows[] = $this->address(['label' => 'Address '.$i]);
        }
        $this->save($this->payload($rows))->assertSessionHasErrors('addresses');
        foreach (['website_setting_id' => 500, 'sort_order' => 8, 'normalized_hash' => 'forged', 'primary_key' => 1] as $key => $value) {
            $this->save($this->payload([$this->address([$key => $value])]))->assertSessionHasErrors('addresses.0');
        }
        $this->save($this->payload([$this->address([], [$this->email('a@example.test', ['website_address_id' => 500])])]))->assertSessionHasErrors('addresses.0.emails.0');
        $this->assertDatabaseCount('website_addresses', 0);
    }

    public function test_shared_version_rejects_stale_address_save_and_initializer_invalidates_old_forms(): void
    {
        WebsiteSetting::saveSingleton(['site_name' => 'Before', 'address' => '10 Road']);
        $version = WebsiteSetting::singleton()->contacts_version;
        app(InitializeWebsiteAddresses::class)->handle(false);
        $this->save($this->payload([], [], [], ['contacts_version' => $version, 'site_name' => 'Stale']))->assertSessionHasErrors('contacts_version');
        $this->assertSame('Before', WebsiteSetting::singleton()->site_name);
        $this->assertDatabaseCount('website_addresses', 1);
    }

    public function test_aggregate_contact_limit_and_scalar_field_budget_fail_without_partial_persistence(): void
    {
        $emails = [];
        $phones = [];
        for ($i = 0; $i < 10; $i++) {
            $emails[] = $this->email('dept'.$i.'@example.test');
            $phones[] = $this->phone('+120255501'.str_pad((string) $i, 2, '0', STR_PAD_LEFT));
        }
        $rows = [];
        for ($i = 0; $i < 5; $i++) {
            $rows[] = $this->address(['label' => 'Location '.$i], $emails, $phones);
        }
        $this->save($this->payload($rows, [$this->email('default@example.test')]))->assertSessionHasErrors('contacts');
        $this->assertDatabaseCount('website_settings', 0);
        $this->assertDatabaseCount('website_addresses', 0);
        $this->assertDatabaseCount('website_contacts', 0);
        $this->save($this->payload([$this->address()], [], [], ['padding' => array_fill(0, 901, 'x')]))->assertSessionHasErrors('collections_complete');
        $this->assertDatabaseCount('website_settings', 0);
    }

    public function test_single_and_labeled_legacy_conversion_preserves_complete_text_and_has_no_invented_primary(): void
    {
        $raw = "Office Address:\r\n10 Example Road, District\r\nCity 00120\r\n\r\nWorks:\r\n工場\r\nService Lane";
        $owner = WebsiteSetting::saveSingleton(['site_name' => 'Legacy', 'address' => $raw, 'public_email' => 'default@example.test']);
        $action = app(InitializeWebsiteAddresses::class);
        $plan = $action->handle();
        $this->assertCount(2, $plan['addresses']);
        $this->assertDatabaseCount('website_addresses', 0);
        $action->handle(false);
        $this->assertSame($raw, $owner->fresh()->address);
        $this->assertSame(['Office Address', 'Works'], $owner->fresh()->addresses->pluck('label')->all());
        $this->assertSame("10 Example Road, District\r\nCity 00120", $owner->fresh()->addresses->first()->address_line_1);
        $this->assertNull($owner->fresh()->primaryAddress());
        $this->assertDatabaseCount('website_contacts', 0);
        app(InitializeWebsiteContacts::class)->handle(false);
        $this->assertNull(WebsiteContact::first()->website_address_id);
    }

    public function test_ambiguous_legacy_address_reconciliation_is_explicit_and_repeat_safe(): void
    {
        $raw = "Office: 10 Road\n\nWorks: 20 Road";
        $owner = WebsiteSetting::saveSingleton(['site_name' => 'Legacy', 'address' => $raw]);
        $action = app(InitializeWebsiteAddresses::class);
        $this->assertNotEmpty($action->handle()['issues']);
        try {
            $action->handle(false);
            $this->fail('Ambiguous legacy address accepted');
        } catch (ValidationException) {
            $this->assertDatabaseCount('website_addresses', 0);
        }
        $action->handle(false, ['addresses' => [$this->address([], [$this->email('reviewed@example.test')])]]);
        $this->assertSame($raw, $owner->fresh()->address);
        $action->handle(false);
        $this->assertDatabaseCount('website_contacts', 1);
        $this->save($this->payload([], [], [], ['removed_addresses_count' => 1, 'removed_addresses' => [
            ['id' => WebsiteAddress::first()->id, 'contact_ids' => WebsiteContact::pluck('id')->all()],
        ]]))->assertSessionHasNoErrors();
        $action->handle(false);
        $this->assertDatabaseCount('website_addresses', 0);
        $this->assertDatabaseCount('website_contacts', 0);
        $this->assertNull($owner->fresh()->primaryAddress());
        $this->assertSame($raw, $owner->fresh()->address);
    }

    public function test_uninitialized_legacy_address_is_read_only_and_unrelated_settings_remain_manageable(): void
    {
        $owner = WebsiteSetting::saveSingleton(['site_name' => 'Original', 'address' => 'Legacy address']);
        $this->get(route('admin.settings.edit'))->assertOk()->assertSee('Legacy address')->assertSee('review legacy address initialization');
        $this->save($this->payload())->assertSessionHasErrors('addresses');
        $this->save(['site_name' => 'General update'])->assertSessionHasNoErrors();
        $this->save(['site_name' => 'General update', 'address' => 'Overwrite'])->assertSessionHasErrors('address');
        $this->assertSame('Legacy address', $owner->fresh()->address);
    }

    public function test_database_constraints_enforce_default_and_scoped_uniqueness_owner_and_restrictive_deletion(): void
    {
        WebsiteSetting::forceCreate(['id' => 500, 'site_name' => 'Other owner']);
        $this->save($this->payload([$this->address([], [$this->email()])], [$this->email()]))->assertSessionHasNoErrors();
        $address = WebsiteAddress::first();
        $default = WebsiteSetting::singleton()->contacts->first();
        foreach ([['website_setting_id' => 1, 'website_address_id' => null], ['website_setting_id' => 1, 'website_address_id' => $address->id],
            ['website_setting_id' => 500, 'website_address_id' => $address->id]] as $owner) {
            try {
                WebsiteContact::forceCreate([...$owner, 'type' => 'email', 'value' => $default->value, 'normalized_value' => $default->normalized_value, 'sort_order' => 2]);
                $this->fail('Unsafe contact insert accepted');
            } catch (QueryException) {
                $this->assertDatabaseCount('website_contacts', 2);
            }
        }
        try {
            $address->delete();
            $this->fail('Address deletion cascaded');
        } catch (QueryException) {
            $this->assertDatabaseCount('website_addresses', 1);
        }
        try {
            WebsiteAddress::forceCreate(['website_setting_id' => 1, 'label' => 'Other', 'address_line_1' => 'Other', 'is_primary' => 1, 'normalized_hash' => str_repeat('b', 64), 'sort_order' => 2]);
            WebsiteAddress::forceCreate(['website_setting_id' => 1, 'label' => 'Third', 'address_line_1' => 'Other', 'is_primary' => 1, 'normalized_hash' => str_repeat('c', 64), 'sort_order' => 3]);
            $this->fail('Multiple primary addresses accepted');
        } catch (QueryException) {
            $this->assertSame(1, WebsiteAddress::where('is_primary', true)->count());
        }
    }

    public function test_failure_rolls_back_general_defaults_addresses_and_location_contacts(): void
    {
        $this->save($this->payload([$this->address(['is_primary' => 1], [$this->email()])], [$this->email('default@example.test')]))->assertSessionHasNoErrors();
        $owner = WebsiteSetting::singleton();
        $address = $owner->addresses->first();
        $before = [$owner->getRawOriginal(), WebsiteAddress::get()->toArray(), WebsiteContact::get()->toArray()];
        $row = $this->persisted($address);
        $row['label'] = 'Changed';
        $row['emails'][] = $this->email('boom@example.test');
        $row['emails_count'] = 2;
        $failed = false;
        DB::listen(function ($query) use (&$failed): void {
            if (! $failed && in_array('boom@example.test', $query->bindings, true)) {
                $failed = true;
                throw new \RuntimeException('Synthetic location contact failure');
            }
        });
        $this->save($this->payload([$row], [$this->email('changed-default@example.test')], [], ['site_name' => 'Roll back']))->assertServerError();
        $this->assertSame($before, [$owner->fresh()->getRawOriginal(), WebsiteAddress::get()->toArray(), WebsiteContact::get()->toArray()]);
    }

    public function test_public_helpers_escape_text_preserve_lines_and_load_addresses_without_n_plus_one(): void
    {
        $rows = [];
        for ($i = 0; $i < 4; $i++) {
            $rows[] = $this->address(['label' => 'Office '.$i, 'address_line_1' => "<script>unsafe()</script>\nLondon", 'city' => 'London'], [$this->email('mail'.$i.'@example.test')]);
        }
        $this->save($this->payload($rows))->assertSessionHasNoErrors();
        DB::enableQueryLog();
        $owner = WebsiteSetting::singleton();
        foreach ($owner->activeAddresses() as $address) {
            $this->assertCount(1, $address->activeEmailContacts());
            $this->assertNotNull($address->activeEmailContacts()->first()->mailtoUrl());
            $this->assertSame(1, substr_count($address->formattedAddress(), 'London'));
        }
        $this->assertLessThanOrEqual(4, count(DB::getQueryLog()));
        DB::disableQueryLog();
        $html = Blade::render('@foreach($owner->activeAddresses() as $address)<p>{{ $address->label }}</p><pre>{{ $address->formattedAddress() }}</pre>@endforeach', compact('owner'));
        $this->assertStringNotContainsString('<script>unsafe()</script>', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
    }

    public function test_command_missing_record_dry_run_empty_initialization_and_authorization(): void
    {
        $this->artisan('settings:initialize-addresses', ['--dry-run' => true])->assertFailed();
        $owner = WebsiteSetting::saveSingleton(['site_name' => 'Legacy', 'address' => "Road, District\nCity"]);
        $this->artisan('settings:initialize-addresses', ['--dry-run' => true])->expectsOutputToContain('Address entries: 1')->assertSuccessful();
        $this->assertNull($owner->fresh()->addresses_initialized_at);
        $this->artisan('settings:initialize-addresses')->assertSuccessful();
        $this->artisan('settings:initialize-addresses')->expectsOutputToContain('already initialized')->assertSuccessful();
        $this->actingAs(User::factory()->create()->assignRole(Rbac::ROLE_ADMIN));
        $this->save($this->payload())->assertForbidden();
        $this->assertDatabaseCount('website_addresses', 1);
    }
}
