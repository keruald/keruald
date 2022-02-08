<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Countable;
use InvalidArgumentException;
use ResourceBundle;
use SimpleXMLElement;
use TypeError;

class TraversableUtilities {

    public static function count ($countable) : int {
        if (is_countable($countable)) {
            return count($countable);
        }

        if ($countable === null || $countable === false) {
            return 0;
        }

        throw new TypeError;
    }

    public static function first (iterable $iterable) : mixed {
        foreach ($iterable as $value) {
            return $value;
        }

        throw new InvalidArgumentException(
            "Can't call first() on an empty iterable."
        );
    }

    public static function firstOr (
        iterable $iterable, mixed $defaultValue = null
    ) : mixed {
        foreach ($iterable as $value) {
            return $value;
        }

        return $defaultValue;
    }

    /**
     * @deprecated Use \is_countable
     */
    public static function isCountable ($countable) : bool {
        if (function_exists('is_countable')) {
            // PHP 7.3 has is_countable
            return is_countable($countable);
        }

        // https://github.com/Ayesh/is_countable-polyfill/blob/master/src/is_countable.php
        return is_array($countable)
               || $countable instanceof Countable
               || $countable instanceof SimpleXMLElement
               || $countable instanceof ResourceBundle;
    }

}
