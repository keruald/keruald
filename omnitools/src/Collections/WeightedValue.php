<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use TypeError;

class WeightedValue implements Comparable {

    ///
    /// Constants
    ///

    const DEFAULT_WEIGHT = 1.0;

    ///
    /// Private members
    ///

    /**
     * @var float
     */
    private $weight = self::DEFAULT_WEIGHT;

    /**
     * @var mixed
     */
    private $value;

    ///
    /// Constructors
    ///

    public function __construct ($value, float $weight = self::DEFAULT_WEIGHT) {
        $this->value = $value;
        $this->weight = $weight;
    }

    public static function parse (string $expression) : WeightedValue {
        $pair = explode(';q=', $expression);

        if (count($pair) == 1) {
            return new WeightedValue($pair[0]);
        }

        return new WeightedValue($pair[0], (float)$pair[1]);
    }

    ///
    /// Getters and setters
    ///

    public function getWeight () : float {
        return $this->weight;
    }

    public function setWeight (float $weight) : self {
        $this->weight = $weight;

        return $this;
    }

    public function getValue () {
        return $this->value;
    }

    public function setValue ($value) : self {
        $this->value = $value;

        return $this;
    }

    ///
    /// Helper methods
    ///

    public function compareTo (object $other) : int {
        if (!$other instanceof WeightedValue) {
            throw new TypeError;
        }

        return $this->getWeight() <=> $other->getWeight();
    }

}
