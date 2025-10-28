<?php

namespace Keruald\OmniTools\DataTypes\Option;

abstract class Option {
    public abstract function isSome () : bool;
    public abstract function isNone () : bool;

    public abstract function getValue() : mixed;

    public abstract function map(callable $callable) : self;

    public abstract function orElse(mixed $default) : mixed;

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
