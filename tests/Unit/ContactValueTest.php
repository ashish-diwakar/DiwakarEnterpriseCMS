<?php

namespace Tests\Unit;

use App\Models\WebsiteSetting;
use App\Support\ContactValue;
use App\Support\LegacyWebsiteContacts;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class ContactValueTest extends TestCase
{
    #[DataProvider('values')]
    public function test_normalization_is_safe_and_country_neutral(string $type, string $value, string $subtype, ?string $expected): void
    {
        $actual = $type === 'email' ? ContactValue::email($value) : ContactValue::phone($value, $subtype);
        $this->assertSame($expected, $actual);
    }

    public static function values(): array
    {
        return [
            ['email', ' Sales+Tag@Example.test ', '', 'sales+tag@example.test'],
            ['email', 'one@example.test,two@example.test', '', null],
            ['email', 'one@example.test;two@example.test', '', null],
            ['email', "x@example.test\r\nBcc:y@example.test", '', null],
            ['email', 'Name <one@example.test>', '', null],
            ['email', 'mailto:one@example.test', '', null],
            ['email', 'not-an-email', '', null],
            ['phone', '+1 (202) 555-0100', 'mobile', '+12025550100'],
            ['phone', '+44 20 1234 5678 ext. 123', 'landline', '+442012345678;ext=123'],
            ['phone', '(020) 1234 5678 x42', 'other', '02012345678;ext=42'],
            ['phone', '1800 555 0100', 'toll_free', '18005550100'],
            ['phone', '+33 1 23 45 67 89', 'landline', '+33123456789'],
            ['phone', '+0 2025550100', 'other', null],
            ['phone', '+44 (0)20 1234 5678', 'landline', null],
            ['phone', '12345', 'other', null],
            ['phone', '0000000', 'other', null],
            ['phone', '+1234567890123456', 'other', null],
            ['phone', '1234567,7654321', 'other', null],
            ['phone', '1234567/7654321', 'other', null],
            ['phone', 'tel:+12025550100', 'other', null],
            ['phone', '1234567;ext=5', 'landline', null],
            ['phone', "1234567\n7654321", 'other', null],
            ['phone', '1234567 ext 123456789', 'landline', null],
            ['phone', '1234567 ext 42', 'mobile', null],
            ['phone', '1234567', 'unknown', null],
            ['phone', ')1234567(', 'other', null],
        ];
    }

    public function test_legacy_parser_does_not_guess_at_unsupported_separators_and_keeps_first_intent(): void
    {
        $settings = (new WebsiteSetting)->forceFill(['public_email' => ',sales@example.test; SALES@example.test;', 'primary_phone' => '', 'secondary_phone' => '02012345678']);
        $plan = LegacyWebsiteContacts::inspect($settings);
        $this->assertSame([], $plan['issues']);
        $this->assertCount(1, $plan['emails']);
        $this->assertTrue($plan['emails'][0]['is_primary']);
        $this->assertFalse($plan['phones'][0]['is_primary']);
        $this->assertSame(1, $plan['duplicates']);
        $settings->primary_phone = '02012345678 / 02076543210';
        $this->assertNotEmpty(LegacyWebsiteContacts::inspect($settings)['issues']);
    }
}
