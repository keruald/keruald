<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Countable;
use TypeError;

class TraversableUtilities {

    public static function count ($countable) : int {
        if (is_array($countable)) {
            return count($countable);
        }

        if ($countable instanceof Countable) {
            return $countable->count();
        }

        if ($countable === null || $countable === false) {
            return 0;
        }

        throw new TypeError;
    }

}
