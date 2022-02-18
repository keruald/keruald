<?php

namespace Keruald\OmniTools\Collections;

use ArrayIterator;
use Countable;
use Iterator;
use IteratorAggregate;

class WeightedList implements IteratorAggregate, Countable {

    /**
     * @var WeightedValue[]
     */
    private $list;

    public function __construct () {
        $this->list = [];
    }

    /**
     * @param string $expression A string like "a,b;q=0.1,c;q=0.4"
     *
     * @return WeightedList
     *
     * @see RFC 7231, section 5.3.1
     */
    public static function parse (string $expression) : WeightedList {
        $list = new WeightedList();

        if ($expression !== "") {
            $items = explode(',', $expression);
            foreach ($items as $item) {
                $list->addFromString($item);
            }
        }

        return $list;
    }

    ///
    /// Helper methods
    ///

    public function add ($item, float $weight = 1.0) : void {
        $this->list[] = new WeightedValue($item, $weight);
    }

    public function addWeightedValue (WeightedValue $value) : void {
        $this->list[] = $value;
    }

    public function addFromString (string $expression) : void {
        $this->addWeightedValue(WeightedValue::parse($expression));
    }

    public function clear () : void {
        $this->list = [];
    }

    public function getHeaviest () {
        $value = null;

        foreach ($this->list as $candidate) {
            if ($value === null || $candidate->compareTo($value) > 0) {
                $value = $candidate;
            }
        }

        return $value;
    }

    public function toSortedArray () : array {
        $weights = [];
        $values = [];

        foreach ($this->list as $item) {
            $weights[] = $item->getWeight();
            $values[] = $item->getValue();
        }

        array_multisort($weights, SORT_DESC, $values, SORT_ASC);

        return $values;
    }

    ///
    /// IteratorAggregate implementation
    ///

    public function getIterator () : Iterator {
        return new ArrayIterator($this->list);
    }

    ///
    /// Countable implementation
    ///

    public function count () : int {
        return count($this->list);
    }

}
