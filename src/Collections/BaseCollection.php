<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

interface BaseCollection {

    ///
    /// Constructors
    ///

    public static function from (iterable $items) : static;

    ///
    /// Getters
    ///

    public function toArray () : array;

    ///
    /// Properties
    ///

    public function count () : int;

    public function isEmpty () : bool;

}
