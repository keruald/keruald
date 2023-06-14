<?php

namespace Keruald\OmniTools\Collections;

use OutOfRangeException;

trait WithCollection {

    abstract function count () : int;
    abstract function toArray() : array;

    public function first () : mixed {
        foreach ($this->toArray() as $item) {
            return $item;
        }

        throw new OutOfRangeException("The collection is empty.");
    }

    public function firstOr (mixed $default) : mixed {
        return match ($this->count()) {
            0 => $default,
            default => $this->first(),
        };
    }

}
