<?php

namespace Keruald\OmniTools\DataTypes\Result;

abstract class Result {
    public abstract function isOK () : bool;
    public abstract function isError () : bool;

    public abstract function getValue () : mixed;

    public abstract function map(callable $callable) : self;
    public abstract function mapErr(callable $callable): self;

    /**
     * Returns the result if it is Ok,
     * or the default result if an Err.
     *
     * @param mixed $default The default value
     * @return mixed
     */
    public abstract function or(Result $default) : self;

    /**
     * Returns the result if it is Ok,
     * otherwise calls the callable and returns the new result.
     *
     * The callable is called only on Err, allowing lazy evaluation.
     * The callable must return a Result.
     */
    public abstract function orElse(callable $callable) : self;

    /**
     * Returns the value of the result if it is Ok,
     * or the default value if the option is Err.
     *
     * @param mixed $default The default value
     * @return mixed
     */
    public abstract function getValueOr(mixed $default) : mixed;

    /**
     * Returns the value of the result if it is Ok,
     * or the result of the callable if the option is Err.
     *
     * The callable is called only on Err, allowing lazy evaluation.
     *
     * @param callable $callable A function that returns a default value.
     * @return mixed
     */
    public abstract function getValueOrElse(callable $callable) : mixed;
}
