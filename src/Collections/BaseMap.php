<?php

namespace Keruald\OmniTools\Collections;

interface BaseMap {

    public function get (mixed $key) : mixed;

    public function getOr (mixed $key, mixed $defaultValue): mixed;

    public function set (mixed $key, mixed $value) : static;

    public function has (mixed $key) : bool;

    public function contains (mixed $value) : bool;

}
