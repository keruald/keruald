<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Strings\Multibyte;

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

    #[DataProvider('provideCharactersArrays')]
    public function testGetChars (string $string, array $expectedCharacters) : void {
        $actualCharacters = (new OmniString($string))->getChars();

        $this->assertEquals($expectedCharacters, $actualCharacters);
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

        yield ["🇩🇪", ["🇩", "🇪"]];

        yield ["", []];
    }

    public static function provideCharactersBigrams () : iterable {
        yield ["foo", ['fo', 'oo']];

        yield ["night", ['ni', 'ig', 'gh', 'ht']];

        yield ["🇩🇪", ["🇩🇪"]];

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

}
