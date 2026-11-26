<?php

namespace Tests\Feature\Admin;

use App\Actions\Settings\InitializeWebsiteContacts;
use App\Models\User;
use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\Rbac;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class WebsiteContactsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolesAndPermissionsSeeder::class);
    }

    private function authorizeSettings(): void
    {
        $user = User::factory()->create();
        $user->givePermissionTo([Rbac::PERMISSION_ADMIN_ACCESS, Rbac::PERMISSION_SETTINGS_MANAGE]);
        $this->actingAs($user);
    }

    private function email(string $value = 'hello@example.test', array $extra = []): array
    {
        return array_replace(['label' => 'Enquiries', 'value' => $value, 'is_primary' => 0, 'is_active' => 1], $extra);
    }

    private function phone(string $value = '+44 20 1234 5678', array $extra = []): array
    {
        return array_replace(['label' => 'Office', 'value' => $value, 'subtype' => 'landline', 'is_primary' => 0, 'is_active' => 1], $extra);
    }

    private function payload(array $emails = [], array $phones = [], array $extra = []): array
    {
        return array_replace(['site_name' => 'Company', 'contacts_present' => 1, 'contacts_ready' => 1,
            'contacts_version' => WebsiteSetting::singleton()->contacts_version ?? 0,
            'collections_complete' => 1, 'emails_count' => count($emails), 'phones_count' => count($phones),
            'emails' => $emails, 'phones' => $phones], $extra);
    }

    public function test_contacts_require_existing_settings_authorization(): void
    {
        $this->patch(route('admin.settings.update'), $this->payload([$this->email()]))->assertRedirect(route('login', absolute: false));
        $user = User::factory()->create()->assignRole(Rbac::ROLE_ADMIN);
        $this->actingAs($user)->patch(route('admin.settings.update'), $this->payload([$this->email()]))->assertForbidden();
        $this->assertDatabaseCount('website_contacts', 0);
    }

    public function test_multiple_email_and_phone_entries_are_normalized_labelled_and_ordered(): void
    {
        $this->authorizeSettings();
        $emails = [];
        for ($i = 0; $i < 5; $i++) {
            $emails[] = $this->email('Dept'.$i.'@Example.test', ['label' => 'Department '.$i, 'is_primary' => $i === 2]);
        }
        $phones = [$this->phone(), $this->phone('+1 (202) 555-0101', ['subtype' => 'mobile', 'is_primary' => 1]), $this->phone('1800 555 0102', ['subtype' => 'toll_free'])];
        $this->patch(route('admin.settings.update'), $this->payload($emails, $phones))->assertSessionHasNoErrors();
        $settings = WebsiteSetting::singleton();
        $this->assertDatabaseCount('website_contacts', 8);
        $this->assertSame([1, 2, 3, 4, 5], $settings->activeEmailContacts()->pluck('sort_order')->all());
        $this->assertSame('dept2@example.test', $settings->primaryEmail()->normalized_value);
        $this->assertSame('+12025550101', $settings->primaryPhone()->normalized_value);
        $this->assertSame('Dept2@Example.test', $settings->primaryEmail()->value);
        $this->assertDatabaseHas('website_contacts', ['type' => 'phone', 'subtype' => 'toll_free', 'label' => 'Office']);
    }

    public function test_edit_delete_order_and_primary_switch_preserve_other_contacts_and_ids(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('one@example.test', ['is_primary' => 1]), $this->email('two@example.test'), $this->email('three@example.test')]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->contacts->values();
        $this->patch(route('admin.settings.update'), $this->payload([
            $this->email('renamed@example.test', ['id' => $rows[2]->id, 'is_primary' => 1]),
            $this->email('one@example.test', ['id' => $rows[0]->id]),
        ]))->assertSessionHasNoErrors();
        $settings = WebsiteSetting::singleton();
        $this->assertSame([$rows[2]->id, $rows[0]->id], $settings->contacts->pluck('id')->all());
        $this->assertSame($rows[2]->id, $settings->primaryEmail()->id);
        $this->assertFalse($rows[0]->fresh()->is_primary);
        $this->assertDatabaseMissing('website_contacts', ['id' => $rows[1]->id]);
    }

    public function test_value_swaps_do_not_conflict_with_unique_keys_or_replace_record_identity(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('a@example.test'), $this->email('b@example.test')]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->contacts->values();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('b@example.test', ['id' => $rows[0]->id]), $this->email('a@example.test', ['id' => $rows[1]->id])]))->assertSessionHasNoErrors();
        $this->assertSame('b@example.test', $rows[0]->fresh()->normalized_value);
        $this->assertSame('a@example.test', $rows[1]->fresh()->normalized_value);
        $this->assertDatabaseCount('website_contacts', 2);
    }

    public function test_each_contact_is_validated_and_multiple_values_or_unsafe_content_are_rejected(): void
    {
        $this->authorizeSettings();
        foreach (['not-an-email', 'a@example.test,b@example.test', "a@example.test\r\nBcc:x@example.test", "a@example.test\n", "\ta@example.test"] as $invalid) {
            $this->patch(route('admin.settings.update'), $this->payload([$this->email($invalid)]))->assertSessionHasErrors('emails.0.value');
        }
        foreach (['123', '+123456789, +198765432', 'tel:+442012345678', '+44 (0)20 1234 5678', 'javascript:alert(1)', "+442012345678\r", "\t+442012345678"] as $invalid) {
            $this->patch(route('admin.settings.update'), $this->payload([], [$this->phone($invalid)]))->assertSessionHasErrors('phones.0.value');
        }
        $this->patch(route('admin.settings.update'), $this->payload([], [$this->phone('+1 2025550100 ext 1', ['subtype' => 'mobile'])]))->assertSessionHasErrors('phones.0.value');
        $this->assertDatabaseCount('website_contacts', 0);
        $this->assertDatabaseCount('website_settings', 0);
    }

    public function test_duplicate_values_multiple_primaries_and_inactive_primary_are_rejected(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('HELLO@example.test'), $this->email()]))->assertSessionHasErrors('emails.1.value');
        $this->patch(route('admin.settings.update'), $this->payload([], [$this->phone(), $this->phone('+442012345678')]))->assertSessionHasErrors('phones.1.value');
        foreach ([[$this->email('a@example.test', ['is_primary' => 1]), $this->email('b@example.test', ['is_primary' => 1])], [$this->email('a@example.test', ['is_primary' => 1, 'is_active' => 0])]] as $emails) {
            $this->patch(route('admin.settings.update'), $this->payload($emails))->assertSessionHasErrors();
        }
        $this->patch(route('admin.settings.update'), $this->payload([], [$this->phone('+12025550100', ['is_primary' => 1]), $this->phone('+12025550101', ['is_primary' => 1])]))->assertSessionHasErrors('phones');
        $this->assertDatabaseCount('website_contacts', 0);
    }

    public function test_primary_deactivation_and_deletion_leave_no_implicit_replacement(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('a@example.test', ['is_primary' => 1]), $this->email('b@example.test')]))->assertSessionHasNoErrors();
        $rows = WebsiteSetting::singleton()->contacts->values();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('a@example.test', ['id' => $rows[0]->id, 'is_active' => 0]), $this->email('b@example.test', ['id' => $rows[1]->id])]))->assertSessionHasNoErrors();
        $settings = WebsiteSetting::singleton();
        $this->assertNull($settings->primaryEmail());
        $this->assertCount(1, $settings->activeEmailContacts());
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('b@example.test', ['id' => $rows[1]->id])]))->assertSessionHasNoErrors();
        $this->assertNull(WebsiteSetting::singleton()->primaryEmail());
    }

    public function test_empty_collections_are_authoritative_and_general_only_updates_preserve_contacts(): void
    {
        $this->authorizeSettings();
        $settings = WebsiteSetting::saveSingleton(['site_name' => 'Before', 'public_email' => 'legacy@example.test', 'address' => 'Retain address', 'footer_text' => 'Retain footer']);
        app(InitializeWebsiteContacts::class)->handle(false);
        $this->patch(route('admin.settings.update'), ['site_name' => 'General only'])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('website_contacts', 1);
        $this->patch(route('admin.settings.update'), $this->payload())->assertSessionHasNoErrors();
        $settings = $settings->fresh();
        $this->assertSame('legacy@example.test', $settings->public_email);
        $this->assertSame('Retain address', $settings->address);
        $this->assertSame('Retain footer', $settings->footer_text);
        $this->assertCount(0, $settings->activeEmailContacts());
        $this->assertNull($settings->primaryEmail());
        app(InitializeWebsiteContacts::class)->handle(false);
        $this->assertDatabaseCount('website_contacts', 0);
    }

    public function test_stale_updates_unknown_or_cross_record_ids_and_readiness_fail_without_writes(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email()]))->assertSessionHasNoErrors();
        $other = WebsiteSetting::query()->forceCreate(['id' => 500, 'site_name' => 'Other']);
        $foreign = WebsiteContact::forceCreate(['website_setting_id' => $other->id, 'type' => 'email', 'value' => 'other@example.test', 'normalized_value' => 'other@example.test', 'sort_order' => 1]);
        foreach ([999999, $foreign->id] as $id) {
            $this->patch(route('admin.settings.update'), $this->payload([$this->email('hijack@example.test', ['id' => $id])], [], ['site_name' => 'Must roll back']))->assertSessionHasErrors('emails.0.id');
        }
        $this->patch(route('admin.settings.update'), $this->payload([], [], ['contacts_version' => 0]))->assertSessionHasErrors('contacts_version');
        $this->patch(route('admin.settings.update'), $this->payload([], [], ['contacts_ready' => 0]))->assertSessionHasErrors('contacts_ready');
        $this->assertSame('Company', WebsiteSetting::singleton()->site_name);
        $this->assertSame('other@example.test', $foreign->fresh()->value);
        $this->assertDatabaseCount('website_contacts', 2);
    }

    public function test_untrusted_row_metadata_email_subtypes_and_excessive_submissions_are_rejected(): void
    {
        $this->authorizeSettings();
        foreach (['website_setting_id' => 500, 'sort_order' => 999, 'normalized_value' => 'forged', 'type' => 'phone', 'subtype' => 'mobile'] as $field => $value) {
            $this->patch(route('admin.settings.update'), $this->payload([$this->email('a@example.test', [$field => $value])]))->assertSessionHasErrors('emails.0');
        }
        $emails = [];
        for ($i = 0; $i < 51; $i++) {
            $emails[] = $this->email('a'.$i.'@example.test');
        }
        $this->patch(route('admin.settings.update'), $this->payload($emails))->assertSessionHasErrors('emails');
        $this->assertDatabaseCount('website_contacts', 0);
    }

    public function test_legacy_conversion_preserves_raw_columns_order_deduplication_and_primary_intent(): void
    {
        $settings = WebsiteSetting::saveSingleton(['site_name' => 'Legacy', 'public_email' => 'Sales@Example.test; help@example.test, SALES@example.test', 'primary_phone' => '+1 202 555 0100, +44 20 1234 5678', 'secondary_phone' => '+12025550100|020 7654 3210 ext 123']);
        $raw = $settings->only(['public_email', 'primary_phone', 'secondary_phone', 'site_name']);
        $action = app(InitializeWebsiteContacts::class);
        $plan = $action->handle();
        $this->assertCount(2, $plan['emails']);
        $this->assertCount(3, $plan['phones']);
        $this->assertSame(2, $plan['duplicates']);
        $this->assertDatabaseCount('website_contacts', 0);
        $this->assertNull($settings->fresh()->contacts_initialized_at);
        $action->handle(false);
        $settings = $settings->fresh();
        $this->assertSame($raw, $settings->only(array_keys($raw)));
        $this->assertSame('sales@example.test', $settings->primaryEmail()->normalized_value);
        $this->assertSame('+12025550100', $settings->primaryPhone()->normalized_value);
        $this->assertSame([1, 2, 3], $settings->activePhoneContacts()->pluck('sort_order')->all());
        $this->assertSame('02076543210;ext=123', $settings->activePhoneContacts()->last()->normalized_value);
    }

    public function test_ambiguous_legacy_data_is_not_partially_converted_and_requires_explicit_reconciliation(): void
    {
        $settings = WebsiteSetting::saveSingleton(['site_name' => 'Legacy', 'public_email' => 'ok@example.test, not-an-email', 'primary_phone' => '1234567 / 7654321']);
        $action = app(InitializeWebsiteContacts::class);
        $this->assertNotEmpty($action->handle()['issues']);
        try {
            $action->handle(false);
            $this->fail('Ambiguous legacy values were accepted');
        } catch (ValidationException $exception) {
            $this->assertArrayHasKey('contacts', $exception->errors());
        }
        $this->assertDatabaseCount('website_contacts', 0);
        $this->assertNull($settings->fresh()->contacts_initialized_at);
        $action->handle(false, ['emails' => [$this->email()], 'phones' => [$this->phone()]]);
        $this->assertSame('ok@example.test, not-an-email', $settings->fresh()->public_email);
        $this->assertSame('1234567 / 7654321', $settings->fresh()->primary_phone);
        $this->assertDatabaseCount('website_contacts', 2);
    }

    public function test_uninitialized_legacy_data_cannot_be_replaced_through_the_settings_form(): void
    {
        $this->authorizeSettings();
        $settings = WebsiteSetting::saveSingleton(['site_name' => 'Original', 'public_email' => 'old@example.test']);
        $this->get(route('admin.settings.edit'))->assertOk()->assertSee('Legacy contact initialization requires manual review.')->assertSee('old@example.test');
        $this->patch(route('admin.settings.update'), $this->payload())->assertSessionHasErrors('contacts');
        $this->patch(route('admin.settings.update'), ['site_name' => 'General update'])->assertSessionHasNoErrors();
        $this->assertSame('old@example.test', $settings->fresh()->public_email);
        $this->assertDatabaseCount('website_contacts', 0);
    }

    public function test_repeated_initialization_preserves_customizations_and_legacy_fields_cannot_be_edited(): void
    {
        $this->authorizeSettings();
        $settings = WebsiteSetting::saveSingleton(['site_name' => 'Before', 'public_email' => 'old@example.test']);
        app(InitializeWebsiteContacts::class)->handle(false);
        $contact = $settings->fresh()->contacts->first();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('new@example.test', ['id' => $contact->id, 'label' => 'Changed', 'is_active' => 0])]))->assertSessionHasNoErrors();
        app(InitializeWebsiteContacts::class)->handle(false);
        $this->assertSame('new@example.test', $contact->fresh()->value);
        $this->assertFalse($contact->fresh()->is_active);
        $this->patch(route('admin.settings.update'), ['site_name' => 'Before', 'public_email' => 'overwrite@example.test'])->assertSessionHasErrors('public_email');
        $this->patch(route('admin.settings.update'), ['site_name' => 'Before', 'public_email' => ''])->assertSessionHasNoErrors();
        $this->assertSame('old@example.test', $settings->fresh()->public_email);
    }

    public function test_frontend_links_exclude_inactive_entries_and_escape_labels_without_country_guessing(): void
    {
        $this->authorizeSettings();
        $label = '<script>unsafe()</script>';
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('sales+tag@example.test', ['label' => $label]), $this->email('hidden@example.test', ['is_active' => 0])], [$this->phone('020 1234 5678 ext. 42')]))->assertSessionHasNoErrors();
        $settings = WebsiteSetting::singleton();
        $this->assertCount(1, $settings->activeEmailContacts());
        $this->assertSame('mailto:sales%2Btag@example.test', $settings->activeEmailContacts()->first()->mailtoUrl());
        $this->assertSame('tel:02012345678;ext=42', $settings->activePhoneContacts()->first()->telUrl());
        $this->assertNull(WebsiteContact::where('value', 'hidden@example.test')->first()->mailtoUrl());
        $html = Blade::render('@foreach($settings->activeEmailContacts() as $contact)<a href="{{ $contact->mailtoUrl() }}">{{ $contact->label }}</a>@endforeach', compact('settings'));
        $this->assertStringContainsString(e($label), $html);
        $this->assertStringNotContainsString($label, $html);
    }

    public function test_database_unique_primary_and_canonical_value_constraints(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('first@example.test', ['is_primary' => 1])]))->assertSessionHasNoErrors();
        foreach ([['value' => 'second@example.test', 'normalized_value' => 'second@example.test', 'is_primary' => true], ['value' => 'FIRST@example.test', 'normalized_value' => 'first@example.test', 'is_primary' => false]] as $extra) {
            try {
                WebsiteContact::forceCreate(array_replace(['website_setting_id' => 1, 'type' => 'email', 'is_active' => true, 'sort_order' => 2], $extra));
                $this->fail('Expected database uniqueness protection');
            } catch (QueryException $exception) {
                $this->assertNotEmpty($exception->getMessage());
            }
        }
    }

    public function test_runtime_failure_rolls_back_general_settings_primary_flags_and_temporary_keys(): void
    {
        $this->authorizeSettings();
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('old@example.test', ['is_primary' => 1])]))->assertSessionHasNoErrors();
        $settings = WebsiteSetting::singleton();
        $contact = $settings->contacts->first();
        $before = $contact->getRawOriginal();
        $failed = false;
        DB::listen(function ($query) use (&$failed): void {
            if (! $failed && str_starts_with($query->sql, 'insert into "website_contacts"')) {
                $failed = true;
                throw new \RuntimeException('Synthetic persistence failure');
            }
        });
        $this->patch(route('admin.settings.update'), $this->payload([$this->email('renamed@example.test', ['id' => $contact->id]), $this->email('new@example.test', ['is_primary' => 1])], [], ['site_name' => 'Roll back']))->assertServerError();
        $this->assertSame($before, $contact->fresh()->getRawOriginal());
        $this->assertSame('Company', $settings->fresh()->site_name);
        $this->assertSame(1, $settings->fresh()->contacts_version);
    }

    public function test_manual_command_dry_run_and_repetition_have_clear_non_sensitive_results(): void
    {
        $this->artisan('settings:initialize-contacts', ['--dry-run' => true])->expectsOutputToContain('ID 1 is missing')->assertFailed();
        WebsiteSetting::saveSingleton(['site_name' => 'CLI', 'public_email' => 'private-contact@example.test']);
        $this->artisan('settings:initialize-contacts', ['--dry-run' => true])->expectsOutputToContain('Email entries: 1')->assertSuccessful();
        $this->assertDatabaseCount('website_contacts', 0);
        $this->artisan('settings:initialize-contacts')->assertSuccessful();
        $this->artisan('settings:initialize-contacts')->expectsOutputToContain('already initialized')->assertSuccessful();
        $this->assertDatabaseCount('website_contacts', 1);
    }
}
