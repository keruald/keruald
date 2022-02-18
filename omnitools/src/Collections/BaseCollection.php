<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

abstract class BaseCollection {

    ///
    /// Constructors
    ///

    public static abstract function from (iterable $items) : static;

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
