<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Identifiers;

use Closure;
use InvalidArgumentException;

class Random {

    /**
     * @return string 32 random hexadecimal characters
     */
    public static function generateHexHash () : string {
        return UUID::UUIDv4WithoutHyphens();
    }

    /**
     * @param string $format A for letters, 1 for digits, e.g. AAA111
     *
     * @return string a random string based on the format e.g. ZCK530
     */
    public static function generateString (string $format) : string {
        $randomString = "";

        $len = strlen($format);
        for ($i = 0 ; $i < $len ; $i++) {
            $randomString .= self::generateCharacter($format[$i]);
        }

        return $randomString;
    }

    /**
     * @param string $format A for letters, 1 for digits, e.g. A
     *
     * @return string a random string based on the format e.g. Z
     */
    public static function generateCharacter (string $format) : string {
        return self::getPicker(self::normalizeFormat($format))();
    }

    ///
    /// Helper methods for pickers
    ///

    public static function normalizeFormat (string $format) : string {
        $normalizers = self::getNormalizers();

        foreach ($normalizers as $normalizedFormat => $conditionClosure) {
            if ($conditionClosure($format)) {
                return (string)$normalizedFormat;
            }
        }

        return $format;
    }

    private static function getNormalizers () : array {
        /**
         * <normalized format> => <method which returns true if format matches>
         */

        return [

            'A' => function ($format) : bool {
                return ctype_upper($format);
            },

            'a' => function ($format) : bool {
                return ctype_lower($format);
            },

            '1' => function ($format) : bool {
                return is_numeric($format);
            },

        ];
    }

    private static function getPickers () : array {
        return [

            'A' => function () : string {
                return Random::pickLetter();
            },

            'a' => function () : string {
                return strtolower(Random::pickLetter());
            },

            '1' => function () : string {
                return (string)Random::pickDigit();
            },

        ];
    }

    public static function pickLetter () : string {
        $asciiCode = 65 + mt_rand() % 26;

        return chr($asciiCode);
    }

    public static function pickDigit (int $base = 10) : int {
        return mt_rand() % $base;
    }

    private static function getPicker (string $format) : Closure {
        $pickers = self::getPickers();

        if (isset($pickers[$format])) {
            return $pickers[$format];
        }

        throw new InvalidArgumentException();
    }

}
