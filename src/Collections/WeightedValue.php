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
    /// Constructor
    ///

    public function __construct ($value, float $weight = self::DEFAULT_WEIGHT) {
        $this->value = $value;
        $this->weight = $weight;
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
