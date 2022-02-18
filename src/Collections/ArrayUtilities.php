<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Closure;

class ArrayUtilities {

    ///
    /// Methods to transform every member of an array
    ///

    /**
     * @return int[]
     */
    public static function toIntegers (array $array) : array {
        $newArray = $array;
        array_walk($newArray, self::toIntegerCallback());
        return $newArray;
    }

    ///
    /// Helpers to get callbacks for array_walk methods
    ///

    public static function toIntegerCallback () : Closure {
        return function (&$item) {
            $item = (int)$item;
        };
    }

}
