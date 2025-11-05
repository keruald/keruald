<?php

namespace Keruald\OmniTools\DataTypes\Option;

abstract class Option {
    public abstract function isSome () : bool;
    public abstract function isNone () : bool;

    public abstract function getValue() : mixed;

    public abstract function map(callable $callable) : self;

    /**
     * Returns the option if it contains a value,
     * otherwise returns the default option.
     */
    public abstract function or(Option $default) : self;

    /**
     * Returns the option if it contains a value,
     * otherwise calls the callable and returns the result.
     *
     * The callable is called only on None, allowing lazy evaluation.
     * The callable must return an Option.
     */
    public abstract function orElse(callable $callable) : self;

    /**
     * Returns the value of the option if it is Some,
     * or the default value if the option is None.
     *
     * @param mixed $default The default value
     * @return mixed
     */
    public abstract function getValueOr(mixed $default) : mixed;

    /**
     * Returns the value of the option if it is Some,
     * or the result of the callable if the option is None.
     *
     * The callable is called only on None, allowing lazy evaluation.
     *
     * @param callable $callable A function that returns a default value.
     * @return mixed
     */
    public abstract function getValueOrElse(callable $callable) : mixed;

    ///
    /// Helper to build options
    ///

    /**
     * Converts a nullable value to an Option.
     *
     * @param mixed $value
     *
     * @return Option An instance of None if the value is null; otherwise, an instance of Some.
     */
    public static function from (mixed $value) : self {
        return match ($value) {
            null => new None,
            default => new Some($value),
        };
    }

}
