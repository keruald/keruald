<?php

namespace Keruald\OmniTools\Collections;

use ArrayAccess;

abstract class BaseMap extends BaseCollection implements ArrayAccess {

    ///
    /// Methods to implement
    ///

    public abstract function get (mixed $key) : mixed;

    public abstract function getOr (mixed $key, mixed $defaultValue): mixed;

    public abstract function set (mixed $key, mixed $value) : static;

    public abstract function unset (mixed $key) : static;

    public abstract function has (mixed $key) : bool;

    public abstract function contains (mixed $value) : bool;

    ///
    /// ArrayAccess
    /// Interface to provide accessing objects as arrays.
    ///

    public function offsetExists (mixed $offset) : bool {
        return $this->has($offset);
    }

    public function offsetGet (mixed $offset) : mixed {
        return $this->get($offset);
    }

    public function offsetSet (mixed $offset, mixed $value) : void {
        $this->set($offset, $value);
    }

    public function offsetUnset (mixed $offset) : void {
        $this->unset($offset);
    }

}
