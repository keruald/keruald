<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Strings\Multibyte\StringUtilities;
use PHPUnit\Framework\TestCase;

class StringUtilitiesTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @dataProvider providePadding
     */
    public function testPad (
        string $expected,
        string $input, int $padLength, string $padString, int $padType
    ) : void {
        $paddedString = StringUtilities::pad(
            $input, $padLength, $padString, $padType, "UTF-8"
        );

        $this->assertEquals($expected, $paddedString);
    }

    public function testSupportedEncoding () : void {
        $this->assertTrue(StringUtilities::isSupportedEncoding("UTF-8"));
        $this->assertFalse(StringUtilities::isSupportedEncoding("notexisting"));
    }

    ///
    /// Data providers
    ///

    public function providePadding () : iterable {
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
}
