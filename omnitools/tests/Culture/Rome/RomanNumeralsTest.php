<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Culture\Rome;

use Keruald\OmniTools\Culture\Rome\RomanNumerals;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use InvalidArgumentException;

class RomanNumeralsTest extends TestCase {

    #[DataProvider('provideRomanAndHinduArabicNumerals')]
    public function testFromHindiArabicNumeral (
        string $roman,
        int $hinduArabic
    ) : void {
        $this->assertEquals(
            $roman,
            RomanNumerals::fromHinduArabic($hinduArabic)
        );
    }

    public static function provideRomanAndHinduArabicNumerals () : iterable {
        yield ['i', 1];
        yield ['xi', 11];
        yield ['xlii', 42];
        yield ['mcmxcix', 1999];
        yield ['mm', 2000];
    }

    public function testFromHindiArabicNumeralWithNegativeNumbers () : void {
        $this->expectException(InvalidArgumentException::class);
        RomanNumerals::fromHinduArabic(-1);
    }

    public function testFromHindiArabicNumeralWithZero () : void {
        $this->expectException(InvalidArgumentException::class);
        RomanNumerals::fromHinduArabic(0);
    }

}
