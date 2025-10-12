<?php

namespace Keruald\OmniTools\DataTypes\Option;

abstract class Option {
    public abstract function isSome () : bool;
    public abstract function isNone () : bool;

    public abstract function getValue() : mixed;

    public abstract function map(callable $callable) : self;

    public abstract function orElse(mixed $default) : mixed;
}
