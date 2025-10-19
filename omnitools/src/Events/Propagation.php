<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Events;

use BadFunctionCallException;
use Keruald\OmniTools\DataTypes\Option\None;
use Keruald\OmniTools\DataTypes\Option\Option;
use Keruald\OmniTools\DataTypes\Option\Some;

use RuntimeException;
use Throwable;

 /**
  * Static class providing helper methods to propagate events.
  */
class Propagation {

    /**
     * Calls a set of functions with the specified parameters.
     * This is intended for callback purpose.
     *
     * @param iterable $callables  The functions to call, each item a callable
     * @param array    $parameters The parameters to pass to the functions [optional]
     */
    public static function call (iterable $callables, array $parameters = []) : void {
        foreach ($callables as $callable) {
            if (!is_callable($callable)) {
                $previous = self::grabException($parameters);
                throw new BadFunctionCallException("Callback for this method.", 0, $previous->orElse(null));
            }

            call_user_func_array($callable, $parameters);
        }
    }

    /**
     * Calls a set of functions with the specified parameters.
     * If no function is present, throws an exception.
     *
     * @param iterable        $callables  The functions to call, each item a callable
     * @param array           $parameters The parameters to pass to the functions
     *                                    [optional]
     * @param Throwable|null  $exception  The exception to throw if no callback is
     *                                    provided [optional]
     *
     * @throws Throwable
     */
    public static function callOrThrow (iterable $callables, array $parameters = [], Throwable $exception = null) : void {
        if (!count($callables)) {
            throw $exception ?? self::grabException($parameters)
                                      ->orElse(new RuntimeException);
        }

        static::call($callables, $parameters);
    }

    /**
     * Grabs the first exception among specified items.
     *
     * @param iterable $items The items to check
     * @return Option<Throwable>
     */
    private static function grabException (iterable $items) : Option {
        foreach ($items as $item) {
            if ($item instanceof Throwable) {
                return new Some($item);
            }
        }

        return new None;
    }

}
