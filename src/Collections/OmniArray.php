<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\Strings\Multibyte\OmniString;

class OmniArray {

    /**
     * @var array
     */
    private $items = [];

    ///
    /// Constructors
    ///

    public function __construct (?iterable $items = null) {
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

    public static function explode (string $delimiter, string $string,
                                    int $limit = PHP_INT_MAX) : self {
        return (new OmniString($string))
            ->explode($delimiter, $limit);
    }

    ///
    /// Transformation methods
    ///

    public function toIntegers () : self {
        array_walk($this->items, ArrayUtilities::toIntegerCallback());

        return $this;
    }

    public function map (callable $callable) : self {
        $items = array_map($callable, $this->items);

        return new self($items);
    }

    public function implode(string $delimiter) : OmniString {
        return new OmniString(implode($delimiter, $this->items));
    }

    ///
    /// Getters methods
    ///

    public function toArray () : array {
        return $this->items;
    }

}
