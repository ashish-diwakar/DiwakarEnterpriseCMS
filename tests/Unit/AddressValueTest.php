<?php

namespace Tests\Unit;

use App\Support\AddressValue;
use PHPUnit\Framework\TestCase;

class AddressValueTest extends TestCase
{
    public function test_formatting_preserves_unicode_and_meaningful_lines_without_repeating_components(): void
    {
        $this->assertSame("Étage 2\n10 Rue Exemple\nParis\n00120\nFrance", AddressValue::format([
            'address_line_1' => "Étage 2\r\n10 Rue Exemple\r\nParis", 'address_line_2' => '10 Rue Exemple',
            'city' => 'Paris', 'postal_code' => '00120', 'country' => 'France',
        ]));
        $this->assertSame('東京都千代田区', AddressValue::format(['address_line_1' => '東京都千代田区']));
    }

    public function test_fingerprint_detects_same_labeled_location_but_permits_distinct_labels(): void
    {
        $first = ['label' => 'Head Office', 'address_line_1' => "10 Road\nParis", 'postal_code' => '00120'];
        $variant = ['label' => 'HEAD OFFICE', 'address_line_1' => ' 10 Road Paris ', 'postal_code' => '00120'];
        $this->assertSame(AddressValue::fingerprint($first), AddressValue::fingerprint($variant));
        $this->assertNotSame(AddressValue::fingerprint($first), AddressValue::fingerprint([...$first, 'label' => 'Workshop']));
        $this->assertNotSame(AddressValue::fingerprint($first), AddressValue::fingerprint([...$first, 'postal_code' => '120']));
    }

    public function test_formatted_address_is_plain_text_for_escaped_consumers(): void
    {
        $this->assertSame('<img src=x onerror=alert(1)>', AddressValue::format(['address_line_1' => '<img src=x onerror=alert(1)>']));
    }
}
