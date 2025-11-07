<?php

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;
use Keruald\OmniTools\Reflection\CallableElement;

use InvalidArgumentException;

trait WithCollection {

    abstract function count () : int;
    abstract function toArray() : array;

    public function first () : Option {
        foreach ($this->toArray() as $item) {
            return new Some($item);
        }

        return new None;
    }

    public function firstOr (mixed $default) : mixed {
        return $this->first()->getValueOr($default);
    }

    ///
    /// HOF
    ///

    /**
     * Determines if at least an element of the collection satisfies a condition.
     *
     * The execution of callbacks stop after a callable returned true.
     *
     * @param callable $callable A method returning a boolean with key and value
     *                           or only value as arguments.
     *
     * @return bool True if callback is true for at least one of the elements
     * @throws \ReflectionException if the callable does not exist.
     */
    public function any (callable $callable) : bool {
        $argc = (new CallableElement($callable))->countArguments();
        $items = $this->toArray();

        foreach ($items as $key => $value) {
            $result = match($argc) {
                0 => throw new InvalidArgumentException(self::CB_ZERO_ARG),
                1 => $callable($value),
                default => $callable($key, $value),
            };

            // PHP standard or extensions functions can sometimes throw
            // mixed result, for example true or a constant. Any other
            // result than the boolean true is interpreted as falsy.
            if ($result === true) {
                return true;
            }
        }

        return false;
    }

    /**
     * Determines if all elements of the collection satisfies a condition.
     *
     * The execution of callbacks stop after a callable returned false.
     *
     * @param callable $callable A method returning a boolean with key and value
     *                           or only value as arguments.
     *
     * @return bool True if callback is true for all the elements
     * @throws \ReflectionException if the callable does not exist.
     */
    public function all (callable $callable) : bool {
        $argc = (new CallableElement($callable))->countArguments();
        $items = $this->toArray();

        foreach ($items as $key => $value) {
            $result = match($argc) {
                0 => throw new InvalidArgumentException(self::CB_ZERO_ARG),
                1 => $callable($value),
                default => $callable($key, $value),
            };

            // PHP standard or extensions functions can sometimes throw
            // mixed result, for example true or a constant. Any other
            // result than the boolean true is interpreted as falsy.
            if ($result !== true) {
                return false;
            }
        }

        return true;
    }

}
