<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

class OmniArray {

    /**
     * @var array
     */
    private $items = [];

    ///
    /// Constructors
    ///

    public function __construct (?iterable $items) {
        if ($items === null) {
            return;
        }

        if (is_array($items)) {
            $this->items = $items;

            return;
        }

        foreach ($items as $item) {
            $this->items[] = $item;
        }
    }

    public static function explode (string $delimiter, string $string, int $limit = PHP_INT_MAX) : self {
        return new OmniArray(explode($delimiter, $string, $limit));
    }

    ///
    /// Transformation methods
    ///

    public function toIntegers () : self {
        array_walk($this->items, ArrayUtilities::toIntegerCallback());

        return $this;
    }

    ///
    /// Getters methods
    ///

    public function toArray () : array {
        return $this->items;
    }

}
