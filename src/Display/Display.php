<?php

namespace Keruald\Commands\Display;

abstract class Display {

    abstract function out (string $message) : void;
    abstract function error (string $message) : void;

}
