<?php

namespace Tests\Unit;

use App\Rules\SafeMenuUrl;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class SafeMenuUrlTest extends TestCase
{
    #[DataProvider('urls')]
    public function test_safe_navigation_urls(string $url, bool $expected): void
    {
        $this->assertSame($expected, SafeMenuUrl::accepts($url));
    }

    public static function urls(): array
    {
        return [
            ['/', true], ['/contact?q=one#form', true], ['/a%20b', true], ['https://example.com/a?q=hello%20world#top', true],
            ['HTTP://example.com:8080/path', true], ['https://example.com/?x=%22quoted%22', true],
            ['javascript:alert(1)', false], ['JaVaScRiPt:alert(1)', false], ['data:text/html,x', false],
            ['vbscript:alert(1)', false], ['file:///C:/private', false], ['//evil.test', false], ['///evil.test', false],
            ['/%2fevil.test', false], ['/%252fevil.test', false], ['/\\evil.test', false], ['/%5cevil.test', false],
            ['https:\\evil.test', false], ['https://user:password@example.com', false], ['https://user@example.com', false],
            ['https://example.com/%0aheader', false], ['/a%0d%0ab', false], ['/a%2500b', false], ["/a\nb", false],
            [' https://example.com', false], ['https://', false], ['http://?query', false], ['relative/path', false],
            ['https%3a%2f%2fexample.com', false], ['#only-fragment', false], [str_repeat('a', 2049), false],
            ['/bad%ZZ', false], ['https://example.com/bad%ZZ', false],
        ];
    }
}
