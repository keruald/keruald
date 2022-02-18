<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings;

use Keruald\OmniTools\Strings\Multibyte\OmniString;

class SorensenDiceCoefficient {

    /**
     * @var string[]
     */
    private $x;

    /**
     * @var string[]
     */
    private $y;

    ///
    /// Constructors
    ///

    public function __construct (string $left, string $right) {
        $this->x = (new OmniString($left))->getBigrams();
        $this->y = (new OmniString($right))->getBigrams();
    }

    public static function computeFor(string $left, string $right) : float {
        $instance = new self($left, $right);

        return $instance->compute();
    }

    ///
    /// Sørensen formula
    ///

    public function compute() : float {
        return 2 * $this->countIntersect()
               /
               $this->countCharacters();
    }

    private function countIntersect () : int {
        $intersect = array_intersect($this->x, $this->y);

        return count($intersect);
    }

    private function countCharacters () : int {
        return count($this->x) + count($this->y);
    }

}
