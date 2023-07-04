<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

use Keruald\OmniTools\Reflection\CallableElement;

/**
 * Allows to run a callable on each item of a collection.
 *
 * Tracks the result of this operation:
 *   - by default, the operation is successful
 *   - if a call returns a falsy value, the operation is failed
 *   - if all call returns truthy values, the operation is successful
 *
 * That allows to run a multiple operation with a consolidate result
 * answering the question "Were all calls successful?".
 *
 * If a call fails, the subsequent calls still run.
 *
 * @see \array_walk
 * @see \iterator_apply
 */
class MultipleOperation {

    private bool $result = true;

    ///
    /// Constructors
    ///

    public function __construct (
        private readonly iterable $iterable,
    ) {

    }

    /**
     * Applies the specified callable to each collection item.
     *
     * The callable should return a boolean. If not, result will be converted
     * to a boolean, which can lead to unexpected behavior.
     *
     * The callable can has 0, 1 or 2 arguments:
     *  - if it has one argument, it represents the value
     *  - if it has two arguments, they represent (key, value)
     *
     * The result of one operation is false if the calable returns a falsy value.
     * The result of all operations are true if every callable returns a truthy value.
     *
     * Returns if ALL the operation were successful.
     */
    public static function do (iterable $iterable, callable $callable) : bool {
        $operation = new self($iterable);
        $operation->apply($callable);

        return $operation->isOk();
    }

    ///
    /// Operation
    ///

    /**
     * Applies the specified callable to each collection item.
     *
     * The callable should return a boolean. If not, result will be converted
     * to a boolean, which can lead to unexpected behavior.
     *
     * The callable can has 0, 1 or 2 arguments:
     *  - if it has one argument, it represents the value
     *  - if it has two arguments, they represent (key, value)
     *
     * Flips the global result to false if any of the callable
     * returns a falsy value.
     */
    public function apply (callable $callable) : void {
        $argc = (new CallableElement($callable))->countArguments();

        foreach ($this->iterable as $key => $value) {
            $result = match($argc) {
                0 => $callable(),
                1 => $callable($value),
                default => $callable($key, $value),
            };

            if ((bool)$result === false) {
                $this->result = false;
            }
        }
    }

    ///
    /// Result
    ///

    /**
     * Determines if ALL the operations are successful so far
     */
    public function isOk () : bool {
        return $this->result;
    }

}
