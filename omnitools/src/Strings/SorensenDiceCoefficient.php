<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Strings;

use Keruald\OmniTools\Strings\Multibyte\OmniString;

/**
 * Computes the Sørensen–Dice coefficient, a statistic used to evaluate
 * the similarity between two strings.
 */
class SorensenDiceCoefficient {

    /**
     * @var string[]
     */
    private array $x;

    /**
     * @var string[]
     */
    private array $y;

    ///
    /// Constructors
    ///

    /**
     * @param string $left The first string to compare
     * @param string $right The second string to compare
     */
    public function __construct (string $left, string $right) {
        $this->x = (new OmniString($left))->getBigrams();
        $this->y = (new OmniString($right))->getBigrams();
    }

    /**
     * Allows to directly compute the coefficient between two strings.
     *
     * @param string $left The first string to compare
     * @param string $right The second string to compare
     *
     * @return float The Sørensen–Dice coefficient for the two specified strings.
     */
    public static function computeFor(string $left, string $right) : float {
        $instance = new self($left, $right);

        return $instance->compute();
    }

    ///
    /// Sørensen formula
    ///

    /**
     * @return float The Sørensen–Dice coefficient.
     */
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
