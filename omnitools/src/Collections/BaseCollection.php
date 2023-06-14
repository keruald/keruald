<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

abstract class BaseCollection {

    use WithCollection;

    ///
    /// Constructors
    ///

    public static abstract function from (iterable $items) : static;

    ///
    /// Constants
    ///

    const CB_ZERO_ARG = "Callback function should have at least one argument";

    ///
    /// Getters
    ///

    public abstract function toArray () : array;

    ///
    /// Properties
    ///

    public abstract function count () : int;

    public abstract function isEmpty () : bool;

}
