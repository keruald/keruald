<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Identifiers;

use Closure;
use Exception;
use InvalidArgumentException;

use Keruald\OmniTools\Strings\Multibyte\StringUtilities;

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


    /**
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function generateIdentifier (int $bytes_count) : string {
        $bytes = random_bytes($bytes_count);

        return StringUtilities::encodeInBase64($bytes);
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

    /**
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function pickLetter () : string {
        $asciiCode = 65 + self::pickDigit(26);

        return chr($asciiCode);
    }

    /**
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function pickDigit (int $base = 10) : int {
        return random_int(0, $base - 1);
    }

    private static function getPicker (string $format) : Closure {
        $pickers = self::getPickers();

        if (isset($pickers[$format])) {
            return $pickers[$format];
        }

        throw new InvalidArgumentException();
    }

    /**
     * @throws InvalidArgumentException if [$min, $max] doesn't have at least $count elements.
     * @throws Exception if an appropriate source of randomness cannot be found.
     */
    public static function generateIntegerMonotonicSeries (
        int $min, int $max, int $count
    ) : array {
        if ($max - $min < $count) {
            throw new InvalidArgumentException("Can't build a monotonic series of n elements if the range has fewer elements.");
        }

        $series = [];

        $n = 0;
        while ($n < $count) {
            $candidate = random_int($min, $max);

            if (in_array($candidate, $series)) {
                continue;
            }

            $series[] = $candidate;
            $n++;
        }

        sort($series);
        return $series;

    }
}
