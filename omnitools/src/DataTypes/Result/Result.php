<?php

namespace Keruald\OmniTools\DataTypes\Result;

abstract class Result {
    public abstract function isOK () : bool;
    public abstract function isError () : bool;

    public abstract function getValue () : mixed;

    public abstract function map(callable $callable) : self;
    public abstract function mapErr(callable $callable): self;

    public abstract function orElse(mixed $default) : mixed;
}
