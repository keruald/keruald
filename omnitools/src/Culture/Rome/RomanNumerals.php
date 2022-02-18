<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Culture\Rome;

use InvalidArgumentException;

final class RomanNumerals {

    public static function fromHinduArabic (int $number) : string {
        self::assertStrictlyPositiveNumber($number);

        if ($number > 1000) {
            return self::computeFromKiloHinduArabic($number);
        }

        $table = self::getHinduArabicTable();

        return $table[$number] ?? self::computeFromHinduArabic($number);
    }

    /**
     * Provides a canonical table with hindu arabic numerals as keys,
     * and Roman numerals as values.
     */
    public static function getHinduArabicTable () : array {
        return [
               1 => 'i',
               2 => 'ii',
               3 => 'iii',
               4 => 'iv',
               5 => 'v',
               6 => 'vi',
               7 => 'vii',
               8 => 'viii',
               9 => 'ix',
              10 => 'x',
              50 => 'l',
             100 => 'c',
             500 => 'd',
            1000 => 'm',
        ];
    }

    private static function getComputeHinduArabicTable () : iterable {
        // limit => number to subtract (as a [roman, hindu arabic] array)
        yield   21 => ['x',     10];
        yield   30 => ['xx',    20];
        yield   40 => ['xxx',   30];
        yield   50 => ['xl',    40];
        yield   60 => ['l',     50];
        yield   70 => ['lx',    60];
        yield   80 => ['lxx',   70];
        yield   90 => ['lxxx',  80];
        yield  100 => ['xc',    90];
        yield  200 => ['c',    100];
        yield  300 => ['cc',   200];
        yield  400 => ['ccc',  300];
        yield  500 => ['cd',   400];
        yield  600 => ['d',    500];
        yield  700 => ['dc',   600];
        yield  800 => ['dcc',  700];
        yield  900 => ['dccc', 800];
        yield 1000 => ['cm',   900];
    }

    private static function computeFromHinduArabic (int $number) : string {
        foreach (self::getComputeHinduArabicTable() as $limit => $term) {
            if ($number < $limit) {
                return $term[0] . self::fromHinduArabic($number - $term[1]);
            }
        }

        throw new \LogicException("This should be unreachable code.");
    }

    private static function computeFromKiloHinduArabic (int $number) : string {
        $thousandAmount = (int)floor($number / 1000);
        $remainder = $number % 1000;

        $roman = str_repeat('m', $thousandAmount);
        if ($remainder > 0) {
            $roman .= self::fromHinduArabic($remainder);
        }

        return $roman;
    }

    private static function assertStrictlyPositiveNumber (int $number) : void {
        if ($number < 1) {
            throw new InvalidArgumentException(
                "Can only convert strictly positive numbers"
            );
        }
    }
}
