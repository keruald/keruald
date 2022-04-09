<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\Strings\Multibyte\OmniString;

/**
 * A generic vector implementation to accept any kind of value.
 *
 * Vector offers specialized methods to convert from and to int/string.
 *
 * This class is intended to be used in every case a more specialized
 * vector implementation doesn't exist or isn't needed, ie every time
 * an array is needed, to contains ordered values, without string keys.
 */
class Vector extends BaseVector {

    ///
    /// Specialized constructors
    ///

    /**
     * Constructs a new instance of a vector by exploding a string
     * according a specified delimiter.
     *
     * @param string $delimiter The substring to find for explosion
     * @param string $string The string to explode
     * @param int $limit If specified, the maximum count of vector elements
     * @return static
     */
    public static function explode (string $delimiter, string $string,
                                    int $limit = PHP_INT_MAX) : self {
        // There is some discussion to know if this method belongs
        // to Vector or OmniString.
        //
        // The advantage to keep it here is we can have constructs like:
        //     Vector::explode(",", "1,1,2,3,5,8,13")
        //         ->toIntegers()
        //          >map(function($n) { return $n * $n; })
        //         ->toArray();
        //
        // In this chaining, it is clear we manipulate Vector methods.

        return (new OmniString($string))
            ->explode($delimiter, $limit);
    }

    public static function range (int $start, int $end, int $step = 1) : self {
        return new Vector(range($start, $end, $step));
    }

    ///
    /// HOF :: specialized
    ///

    public function toIntegers () : self {
        array_walk($this->items, ArrayUtilities::toIntegerCallback());

        return $this;
    }

}
