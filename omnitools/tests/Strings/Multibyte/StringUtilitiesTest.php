<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Strings\Multibyte\StringUtilities;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class StringUtilitiesTest extends TestCase {

    ///
    /// Tests
    ///
    #[DataProvider('providePadding')]
    public function testPad (
        string $expected,
        string $input, int $padLength, string $padString, int $padType
    ) : void {
        $paddedString = StringUtilities::pad(
            $input, $padLength, $padString, $padType, "UTF-8"
        );

        $this->assertEquals($expected, $paddedString);
    }

    public function testPadWithDefaultArguments () : void {
        $this->assertEquals("foo ", StringUtilities::pad("foo", 4));
        $this->assertEquals("foo_", StringUtilities::pad("foo", 4, '_'));
        $this->assertEquals("_foo", StringUtilities::pad("foo", 4, '_', STR_PAD_LEFT));
    }

    public function testSupportedEncoding () : void {
        $this->assertTrue(StringUtilities::isSupportedEncoding("UTF-8"));
        $this->assertFalse(StringUtilities::isSupportedEncoding("notexisting"));
    }

    public function testStartsWith () : void {
        $this->assertTrue(StringUtilities::startsWith("foo", "fo"));
        $this->assertTrue(StringUtilities::startsWith("foo", ""));
        $this->assertTrue(StringUtilities::startsWith("foo", "foo"));

        $this->assertFalse(StringUtilities::startsWith("foo", "bar"));
    }

    public function testEndsWith () : void {
        $this->assertTrue(StringUtilities::endsWith("foo", "oo"));
        $this->assertTrue(StringUtilities::endsWith("foo", ""));
        $this->assertTrue(StringUtilities::endsWith("foo", "foo"));

        $this->assertFalse(StringUtilities::endsWith("foo", "oO"));
        $this->assertFalse(StringUtilities::endsWith("foo", "bar"));
    }

    #[DataProvider('provideBase64')]
    public function testEncodeInBase64 (string $decoded, string $encoded) : void {
        $actual = StringUtilities::encodeInBase64($decoded);
        $this->assertEquals($encoded, $actual);
    }

    #[DataProvider('provideBase64')]
    public function testDecodeFromBase64 (string $decoded, string $encoded) : void {
        $actual = StringUtilities::decodeFromBase64($encoded);
        $this->assertEquals($decoded, $actual);
    }

    ///
    /// Data providers
    ///

    public static function providePadding () : iterable {
        // Tests from http://3v4l.org/UnXTF
        // http://web.archive.org/web/20150711100913/http://3v4l.org/UnXTF

        yield ['àèòàFOOàèòà', "FOO", 11, "àèò", STR_PAD_BOTH];
        yield ['àèòFOOàèòà', "FOO", 10, "àèò", STR_PAD_BOTH];
        yield ['àèòBAAZàèòà', "BAAZ", 11, "àèò", STR_PAD_BOTH];
        yield ['àèòBAAZàèò', "BAAZ", 10, "àèò", STR_PAD_BOTH];
        yield ['FOOBAR', "FOOBAR", 6, "àèò", STR_PAD_BOTH];
        yield ['FOOBAR', "FOOBAR", 1, "àèò", STR_PAD_BOTH];
        yield ['FOOBAR', "FOOBAR", 0, "àèò", STR_PAD_BOTH];
        yield ['FOOBAR', "FOOBAR", -10, "àèò", STR_PAD_BOTH];

        yield ['àèòàèòàèFOO', "FOO", 11, "àèò", STR_PAD_LEFT];
        yield ['àèòàèòàFOO', "FOO", 10, "àèò", STR_PAD_LEFT];
        yield ['àèòàèòàBAAZ', "BAAZ", 11, "àèò", STR_PAD_LEFT];
        yield ['àèòàèòBAAZ', "BAAZ", 10, "àèò", STR_PAD_LEFT];
        yield ['FOOBAR', "FOOBAR", 6, "àèò", STR_PAD_LEFT];
        yield ['FOOBAR', "FOOBAR", 1, "àèò", STR_PAD_LEFT];
        yield ['FOOBAR', "FOOBAR", 0, "àèò", STR_PAD_LEFT];
        yield ['FOOBAR', "FOOBAR", -10, "àèò", STR_PAD_LEFT];

        yield ['FOOàèòàèòàè', "FOO", 11, "àèò", STR_PAD_RIGHT];
        yield ['FOOàèòàèòà', "FOO", 10, "àèò", STR_PAD_RIGHT];
        yield ['BAAZàèòàèòà', "BAAZ", 11, "àèò", STR_PAD_RIGHT];
        yield ['BAAZàèòàèò', "BAAZ", 10, "àèò", STR_PAD_RIGHT];
        yield ['FOOBAR', "FOOBAR", 6, "àèò", STR_PAD_RIGHT];
        yield ['FOOBAR', "FOOBAR", 1, "àèò", STR_PAD_RIGHT];
        yield ['FOOBAR', "FOOBAR", 0, "àèò", STR_PAD_RIGHT];
        yield ['FOOBAR', "FOOBAR", -10, "àèò", STR_PAD_RIGHT];
    }

    public static function provideBase64 () : iterable {
        yield ['foo', 'Zm9v', "This is the regular base test without any exception."];
        yield ['', '', "An empty string should remain an empty string."];
        yield [
            "J'ai fait mes 60 prières par terre dans la poudrière.",
            'SidhaSBmYWl0IG1lcyA2MCBwcmnDqHJlcyBwYXIgdGVycmUgZGFucyBsYSBwb3VkcmnDqHJlLg',
            "No padding should be used."
        ];
        yield [
            "àèòàFOOàèòà", "w6DDqMOyw6BGT0_DoMOow7LDoA",
            "Slashes / should be replaced by underscores _."
        ];
    }
}
