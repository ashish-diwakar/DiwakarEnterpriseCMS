<?php

// Standalone synthetic checks only. No Laravel application, .env, HTTP or network database.
require dirname(__DIR__, 2).'/vendor/autoload.php';

use App\Actions\Settings\InitializeWebsiteAddresses;
use App\Actions\Settings\InitializeWebsiteContacts;
use App\Actions\Settings\SyncWebsiteAddresses;
use App\Actions\Settings\SyncWebsiteContacts;
use App\Actions\Settings\UpdateWebsiteSettings;
use App\Models\WebsiteAddress;
use App\Models\WebsiteContact;
use App\Models\WebsiteSetting;
use App\Support\AddressValue;
use App\Support\ContactValue;
use App\Support\LegacyWebsiteAddresses;
use App\Support\SettingsSubmission;
use App\Support\WebsiteAddressesInput;
use App\Support\WebsiteContactsInput;
use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\QueryException;
use Illuminate\Events\Dispatcher;
use Illuminate\Support\DateFactory;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Translation\ArrayLoader;
use Illuminate\Translation\Translator;
use Illuminate\Validation\Factory;
use Illuminate\Validation\ValidationException;
use Tests\Unit\ContactValueTest;

$checks = 0;
$check = function (bool $condition, string $name) use (&$checks): void {
    if (! $condition) {
        throw new RuntimeException('FAILED: '.$name);
    }
    $checks++;
};
$validation = function (callable $operation, string $key) use ($check): void {
    try {
        $operation();
        throw new RuntimeException('Expected validation failure: '.$key);
    } catch (ValidationException $exception) {
        $check(isset($exception->errors()[$key]), 'Validation key '.$key);
    }
};
$queryFailure = function (callable $operation) use ($check): void {
    try {
        $operation();
        throw new RuntimeException('Expected database constraint rejection');
    } catch (QueryException) {
        $check(true, 'Database constraint rejection');
    }
};
$capsule = new Capsule;
$capsule->addConnection(['driver' => 'sqlite', 'database' => ':memory:', 'foreign_key_constraints' => true]);
$capsule->setEventDispatcher(new Dispatcher($capsule->getContainer()));
$capsule->setAsGlobal();
$capsule->bootEloquent();
DB::swap($capsule->getDatabaseManager());
Schema::swap($capsule->schema());
Date::swap(new DateFactory);
Validator::swap(new Factory(new Translator(new ArrayLoader, 'en'), $capsule->getContainer()));
foreach (DB::select('PRAGMA database_list') as $attached) {
    $check($attached->file === '', 'Only nonpersistent SQLite permitted');
}
$migrations = [];
foreach (['2026_10_06_000001_create_website_settings_table', '2026_11_26_000002_create_website_contacts_table',
    '2026_11_26_000003_create_website_addresses_table', '2026_11_26_000004_scope_website_contacts_to_addresses'] as $name) {
    $migration = require dirname(__DIR__, 2).'/database/migrations/'.$name.'.php';
    if (str_ends_with($name, 'scope_website_contacts_to_addresses')) {
        DB::table('website_settings')->insert(['id' => 700, 'site_name' => 'Preserved migration fixture']);
        DB::table('website_contacts')->insert(['id' => 700, 'website_setting_id' => 700, 'type' => 'email', 'value' => 'fixture@example.test', 'normalized_value' => 'fixture@example.test', 'is_primary' => true, 'sort_order' => 1]);
    }
    $migration->up();
    if (str_ends_with($name, 'scope_website_contacts_to_addresses')) {
        $preserved = DB::table('website_contacts')->where('id', 700)->first();
        $check($preserved->website_address_id === null && $preserved->scope_key === 0 && $preserved->value === 'fixture@example.test', 'Migration preserves existing default contacts');
        DB::table('website_contacts')->where('id', 700)->delete();
        DB::table('website_settings')->where('id', 700)->delete();
    }
    $migrations[] = $migration;
}
$check(Schema::hasColumns('website_contacts', ['scope_key', 'primary_type', 'website_address_id']), 'Ordered scoped migration');
foreach (ContactValueTest::values() as [$type, $value, $subtype, $expected]) {
    $actual = $type === 'email' ? ContactValue::email($value) : ContactValue::phone($value, $subtype);
    $check($actual === $expected, 'Established normalization');
}
$email = fn (string $value = 'same@example.test', array $extra = []) => array_replace(['value' => $value, 'is_primary' => 1, 'is_active' => 1], $extra);
$addressRow = fn (string $label, array $extra = []) => array_replace([
    'label' => $label, 'address_line_1' => "10 Example Road\r\nLondon", 'city' => 'London', 'postal_code' => '00120',
    'is_primary' => 0, 'is_active' => 1, 'contacts_present' => 1, 'emails' => [$email()], 'phones' => [],
], $extra);
$contacts = new SyncWebsiteContacts;
$addresses = new SyncWebsiteAddresses($contacts);
$update = new UpdateWebsiteSettings($contacts, $addresses);
$initContacts = new InitializeWebsiteContacts($contacts);
$initAddresses = new InitializeWebsiteAddresses($addresses);
$owner = WebsiteSetting::saveSingleton(['site_name' => 'Synthetic', 'public_email' => 'same@example.test', 'address' => "Office Address:\r\n10 Road, City\r\n\r\nWorks:\r\n工場"]);
$original = $owner->only(['public_email', 'address']);
$unexpected = WebsiteAddress::forceCreate(['website_setting_id' => 1, 'label' => 'Unexpected', 'address_line_1' => 'Synthetic', 'normalized_hash' => str_repeat('a', 64), 'sort_order' => 1]);
$validation(fn () => $initAddresses->handle(), 'addresses');
$unexpected->delete();
$check(count($initAddresses->handle()['addresses']) === 2 && WebsiteAddress::count() === 0, 'Address dry run');
$check(count($initContacts->handle()['emails']) === 1 && WebsiteContact::count() === 0, 'Default dry run');
$initContacts->handle(false);
$initAddresses->handle(false);
$owner = $owner->fresh();
$check($owner->only(array_keys($original)) === $original, 'Legacy snapshots unchanged');
$check($owner->primaryAddress() === null && $owner->contacts->first()->website_address_id === null, 'No invented location association/primary');
$check($owner->addresses->pluck('label')->all() === ['Office Address', 'Works'], 'Legacy labels/order');
$check($owner->contacts_version === 2, 'Initializer shared version');
$validation(fn () => $update->handle(['site_name' => 'Stale', 'addresses_present' => 1, 'contacts_version' => 1, 'addresses' => []]), 'contacts_version');
$update->handle(['site_name' => 'Synthetic', 'contacts_version' => 2, 'addresses_present' => 1, 'addresses' => [$addressRow('Head Office', ['is_primary' => 1]), $addressRow('東京事務所')]]);
$owner = $owner->fresh();
$rows = $owner->addresses;
$check(WebsiteContact::count() === 3 && $owner->contacts()->count() === 1, 'Default and address scopes independent');
$check($rows[0]->primaryEmail()->value === $owner->primaryEmail()->value && $rows[1]->primaryEmail()->value === $owner->primaryEmail()->value, 'Primary per scope');
$check($owner->contacts_version === 3, 'One version increment per save');
$check(substr_count($rows[0]->formattedAddress(), 'London') === 1 && str_contains($rows[0]->formattedAddress(), '00120'), 'Formatting without duplicates');
$validation(fn () => WebsiteAddressesInput::validate(['addresses' => [$addressRow('Office'), $addressRow('OFFICE')]]), 'addresses.1.label');
$validation(fn () => WebsiteAddressesInput::validate(['addresses' => [$addressRow('Office', ['is_primary' => 1, 'is_active' => 0])]]), 'addresses.0.is_primary');
$validation(fn () => WebsiteContactsInput::validate(['emails' => [$email(), $email()], 'phones' => []]), 'emails.1.value');
$other = WebsiteSetting::forceCreate(['id' => 500, 'site_name' => 'Other']);
$queryFailure(fn () => WebsiteContact::forceCreate(['website_setting_id' => $other->id, 'website_address_id' => $rows[0]->id, 'type' => 'email', 'value' => 'x@example.test', 'normalized_value' => 'x@example.test', 'sort_order' => 1]));
foreach ([null, $rows[0]->id] as $scope) {
    $queryFailure(fn () => WebsiteContact::forceCreate(['website_setting_id' => 1, 'website_address_id' => $scope, 'type' => 'email', 'value' => 'same@example.test', 'normalized_value' => 'same@example.test', 'sort_order' => 2]));
    $queryFailure(fn () => WebsiteContact::forceCreate(['website_setting_id' => 1, 'website_address_id' => $scope, 'type' => 'email', 'value' => 'another@example.test', 'normalized_value' => 'another@example.test', 'sort_order' => 2, 'is_primary' => 1]));
}
$queryFailure(fn () => $rows[0]->delete());
try {
    $migrations[3]->down();
    throw new LogicException('Unsafe schema rollback accepted');
} catch (RuntimeException $exception) {
    $check(str_contains($exception->getMessage(), 'Preserve and reconcile'), 'Scoped migration rollback guard');
}
$validation(fn () => $update->handle(['site_name' => 'Do not change', 'contacts_version' => 3, 'addresses_present' => 1, 'addresses' => []]), 'addresses');
$check(WebsiteSetting::singleton()->site_name === 'Synthetic', 'Restricted deletion rolls back General');
$serialize = fn ($a) => [...$a->only(['id', ...AddressValue::FIELDS, 'is_active', 'is_primary']), 'contacts_present' => 1,
    'emails' => $a->contacts->where('type', 'email')->map(fn ($c) => $c->only(['id', 'value', 'label', 'is_primary', 'is_active']))->values()->all(), 'phones' => []];
$first = $serialize($rows[0]);
$second = $serialize($rows[1]);
$bad = $first;
$bad['emails'][0]['id'] = $rows[1]->contacts->first()->id;
$validation(fn () => $update->handle(['site_name' => 'No IDOR', 'contacts_version' => 3, 'addresses_present' => 1, 'addresses' => [$bad, $second]]), 'addresses.0.emails.0.id');
$first['is_active'] = 0;
$first['is_primary'] = 0;
$update->handle(['site_name' => 'Synthetic', 'contacts_version' => 3, 'addresses_present' => 1, 'addresses' => [$first, $second]]);
$check($rows[0]->fresh()->activeEmailContacts()->isEmpty() && $rows[0]->contacts->first()->fresh()->mailtoUrl() === null, 'Inactive parent visibility');
$check(WebsiteContact::count() === 3, 'Deactivation preserves records');
$first['is_active'] = 1;
$update->handle(['site_name' => 'Synthetic', 'contacts_version' => 4, 'addresses_present' => 1, 'addresses' => [$first, $second]]);
$check($rows[0]->fresh()->primaryEmail()->mailtoUrl() === 'mailto:same@example.test', 'Reactivation');
$before = [WebsiteSetting::singleton()->getRawOriginal(), WebsiteAddress::get()->toArray(), WebsiteContact::get()->toArray()];
$first['label'] = 'Changed';
$first['emails'][] = $email('boom@example.test', ['is_primary' => 0]);
$thrown = false;
DB::listen(function ($query) use (&$thrown): void {
    if (! $thrown && in_array('boom@example.test', $query->bindings, true)) {
        $thrown = true;
        throw new RuntimeException('Synthetic failure');
    }
});
try {
    $update->handle(['site_name' => 'Roll back', 'contacts_version' => 5, 'contacts_present' => 1, 'emails' => [$email('changed@example.test')], 'phones' => [], 'addresses_present' => 1, 'addresses' => [$first, $second]]);
    throw new LogicException('Failure was not injected');
} catch (RuntimeException $exception) {
    $check($exception->getMessage() === 'Synthetic failure', 'Injected failure occurred');
}
$check($before === [WebsiteSetting::singleton()->getRawOriginal(), WebsiteAddress::get()->toArray(), WebsiteContact::get()->toArray()], 'Atomic rollback across all collections');
$removals = $rows->map(fn ($a) => ['id' => $a->id, 'contact_ids' => $a->contacts->pluck('id')->all()])->all();
$update->handle(['site_name' => 'Empty locations', 'contacts_version' => 5, 'addresses_present' => 1, 'addresses' => [], 'removed_addresses' => $removals]);
$check(WebsiteAddress::count() === 0 && WebsiteContact::count() === 1, 'Exact child removal preserves defaults');
$initAddresses->handle(false);
$check(WebsiteAddress::count() === 0, 'No legacy address resurrection');
$check(WebsiteSetting::singleton()->address === $original['address'], 'Complete legacy address retained');
$ambiguous = (new WebsiteSetting)->forceFill(['address' => "Office: 10 Road\n\nWorks: 20 Road"]);
$check(LegacyWebsiteAddresses::inspect($ambiguous)['issues'] !== [], 'Ambiguous legacy text requires review');
$single = LegacyWebsiteAddresses::inspect((new WebsiteSetting)->forceFill(['address' => "10 Road, District\r\nCity"]));
$check($single['issues'] === [] && $single['addresses'][0]['address_line_1'] === "10 Road, District\r\nCity" && ! $single['addresses'][0]['is_primary'], 'Single legacy multiline address preserved without guesses');
$check(LegacyWebsiteAddresses::inspect((new WebsiteSetting)->forceFill(['address' => "\t"]))['issues'] !== [], 'Unsafe legacy controls are not silently discarded');
$data = ['contacts_present' => 1, 'emails' => [], 'phones' => [], 'emails_count' => 1, 'phones_count' => 0];
$validator = Validator::make($data, []);
SettingsSubmission::after($validator);
$check($validator->errors()->has('emails_count'), 'Truncation count mismatch');
$nested = ['addresses_present' => 1, 'addresses' => [['contacts_present' => 1, 'contacts_ready' => 0, 'emails' => [], 'phones' => [], 'emails_count' => 0, 'phones_count' => 0]], 'addresses_count' => 1, 'removed_addresses' => [], 'removed_addresses_count' => 0];
$validator = Validator::make($nested, []);
SettingsSubmission::after($validator);
$check($validator->errors()->has('addresses.0.contacts_ready'), 'Nested hydration safeguard');
$migrations[3]->down();
$migrations[2]->down();
$migrations[1]->down();
$check(Schema::hasColumns('website_settings', ['address', 'public_email']) && ! Schema::hasTable('website_addresses'), 'In-memory rollback retains legacy schema');
echo 'Standalone SQLite :memory: checks: '.$checks.' passed; no Laravel bootstrap, .env or network database used.'.PHP_EOL;
