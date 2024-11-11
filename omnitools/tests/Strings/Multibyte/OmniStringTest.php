<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

use Keruald\OmniTools\Collections\Vector;
use Keruald\OmniTools\Strings\Multibyte\OmniString;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class OmniStringTest extends TestCase {

    private OmniString $string;

    protected function setUp () : void {
        $this->string = new OmniString("foo");
    }

    public function testToString () : void {
        $this->assertEquals("foo", (string)$this->string);
        $this->assertEquals("foo", $this->string->__toString());
    }

    public function testPad () : void {
        $paddedString = $this->string->pad(9, '-=-', STR_PAD_BOTH);
        $this->assertEquals("-=-foo-=-", $paddedString);
    }

    public function testStartsWith () : void {
        $this->assertTrue($this->string->startsWith("fo"));
        $this->assertTrue($this->string->startsWith(""));
        $this->assertTrue($this->string->startsWith("foo"));

        $this->assertFalse($this->string->startsWith("Fo"));
        $this->assertFalse($this->string->startsWith("bar"));
    }

    public function testEndsWith () : void {
        $this->assertTrue($this->string->endsWith("oo"));
        $this->assertTrue($this->string->endsWith(""));
        $this->assertTrue($this->string->endsWith("foo"));

        $this->assertFalse($this->string->endsWith("oO"));
        $this->assertFalse($this->string->endsWith("bar"));
    }

    public function testLen () : void {
        $this->assertEquals(3, $this->string->len());
    }

    #[DataProvider("provideLengthCounts")]
    public function testCountBytes ($string, $bytes, $codePoints, $graphemes) : void {
        $count = (new OmniString($string))->countBytes();
        $this->assertEquals($bytes, $count);
    }

    #[DataProvider("provideLengthCounts")]
    public function testCountCodePoints ($string, $bytes, $codePoints, $graphemes) : void {
        $count = (new OmniString($string))->countCodePoints();
        $this->assertEquals($codePoints, $count);
    }

    #[DataProvider("provideLengthCounts")]
    public function testCountGraphemes ($string, $bytes, $codePoints, $graphemes) : void {
        $count = (new OmniString($string))->countGraphemes();
        $this->assertEquals($graphemes, $count);
    }

    #[DataProvider('provideCharactersArrays')]
    public function testGetChars (string $string, array $expectedCharacters) : void {
        $actualCharacters = (new OmniString($string))->getChars();

        $this->assertEquals($expectedCharacters, $actualCharacters);
    }

    #[DataProvider("provideBytes")]
    public function testGetBytes (string $string, array $expectedBytes) : void {
        $actual = (new OmniString($string))->getBytes();

        $this->assertEquals($expectedBytes, $actual);
    }

    #[DataProvider("provideCodePoints")]
    public function testGetCodePoints (string $string, array $expectedCodePoints) : void {
        $actual = (new OmniString($string))->getCodePoints();

        $this->assertEquals($expectedCodePoints, $actual);
    }

    #[DataProvider("provideGraphemes")]
    public function testGetGraphemes (string $string, array $expectedGraphemes) : void {
        $actual = (new OmniString($string))->getGraphemes();

        $this->assertEquals($expectedGraphemes, $actual);
    }

    #[DataProvider('provideCharactersBigrams')]
    public function testBigrams (string $string, array $expectedBigrams) : void {
        $actualBigrams = (new OmniString($string))->getBigrams();

        $this->assertEquals($expectedBigrams, $actualBigrams);
    }

    #[DataProvider('provideExplosions')]
    public function testExplode (string $delimiter, string $imploded, array $exploded) : void {
        $actual = (new OmniString($imploded))
            ->explode($delimiter)
            ->toArray();

        $this->assertEquals($exploded, $actual);
    }

    public function testExplodeWithEmptyOmniArray () : void {
        $array = (new OmniString("foo"))
            ->explode("", -1);

        $this->assertEquals(0, count($array->toArray()));
    }

    ///
    /// Data providers
    ///

    public static function provideCharactersArrays () : iterable {
        yield ["foo", ['f', 'o', 'o']];

        yield [
            'àèòàFOOàèòà',
            ['à', 'è', 'ò', 'à', 'F', 'O', 'O', 'à', 'è', 'ò', 'à']
        ];

        yield ["🇩🇪", ["🇩🇪"]];

        yield ["", []];
    }

    public static function provideCharactersBigrams () : iterable {
        yield ["foo", ['fo', 'oo']];

        yield ["night", ['ni', 'ig', 'gh', 'ht']];

        yield ["x", []]; // Only one character -> no bigram

        yield ["🇩🇪", []]; // Only one character -> no bigram

        yield ["", []];
    }

    public static function provideExplosions () : iterable {
        yield ["/", "a/b/c", ['a', 'b', 'c']];
        yield ["/", "abc", ['abc']];
        yield ["/", "/b/c", ['', 'b', 'c']];
        yield ["/", "a/b/", ['a', 'b', '']];

        yield ["", "a/b/c", ['a/b/c']];
        yield ["x", "a/b/c", ['a/b/c']];
    }

    public static function provideLengthCounts () : iterable {
        // Character, bytes, code points, graphemes
        yield ["🏴󠁧󠁢󠁥󠁮󠁧󠁿", 28, 7, 1];

        yield ["", 0, 0, 0];
        yield ["a", 1, 1, 1];
        yield ["foo", 3, 3, 3];
        yield ["é", 2, 1, 1];

        yield ["\0", 1, 1, 1]; // PHP strings are NOT null-terminated
    }

    public static function provideBytes () : iterable {
        yield ["🏴󠁧󠁢󠁥󠁮󠁧󠁿", [
            "\xF0", "\x9F", "\x8F", "\xB4",
            "\xF3", "\xA0", "\x81", "\xA7",
            "\xF3", "\xA0", "\x81", "\xA2",
            "\xF3", "\xA0", "\x81", "\xA5",
            "\xF3", "\xA0", "\x81", "\xAE",
            "\xF3", "\xA0", "\x81", "\xA7",
            "\xF3", "\xA0", "\x81", "\xBF",
        ]];

        yield ["", []];
        yield ["a", ["a"]];
        yield ["foo", ["f", "o", "o"]];
        yield ["é", ["\xC3", "\xA9"]];
    }

    public static function provideCodePoints () : iterable {
        yield ["🏴󠁧󠁢󠁥󠁮󠁧󠁿", [
            "\xF0\x9F\x8F\xB4",
            "\xF3\xA0\x81\xA7",
            "\xF3\xA0\x81\xA2",
            "\xF3\xA0\x81\xA5",
            "\xF3\xA0\x81\xAE",
            "\xF3\xA0\x81\xA7",
            "\xF3\xA0\x81\xBF",
        ]];

        yield ["", []];
        yield ["a", ["a"]];
        yield ["foo", ["f", "o", "o"]];
        yield ["é", ["é"]];
    }

    public static function provideGraphemes () : iterable {
        yield ["🏴󠁧󠁢󠁥󠁮󠁧󠁿", ["🏴󠁧󠁢󠁥󠁮󠁧󠁿"]];

        yield ["", []];
        yield ["a", ["a"]];
        yield ["foo", ["f", "o", "o"]];
        yield ["é", ["é"]];
    }

}
