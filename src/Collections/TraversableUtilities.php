<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Countable;
use ResourceBundle;
use SimpleXMLElement;
use TypeError;

class TraversableUtilities {

    public static function count ($countable) : int {
        if (self::isCountable($countable)) {
            return count($countable);
        }

        if ($countable === null || $countable === false) {
            return 0;
        }

        throw new TypeError;
    }

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
