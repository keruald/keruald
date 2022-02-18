<?php

namespace Keruald\Commands\Display;

/**
 * Class ArrayDisplay
 *
 * Intended to ease unit tests with a display capturing output in arrays.
 *
 * @package Keruald\Commands\Display
 */
class ArrayDisplay extends Display {

    /**
     * @var array
     */
    private $out = [];

    /**
     * @var array
     */
    private $error = [];

    ///
    /// Implement Display
    ///

    public function out (string $message) : void {
        $this->out[] = $message;
    }

    public function error (string $message) : void {
        $this->error[] = $message;
    }

    ///
    /// Getters
    ///

    public function getOut () : array {
        return $this->out;
    }

    public function getError () : array {
        return $this->error;
    }

    ///
    /// Helper methods
    ///

    public function clearOut () : ArrayDisplay {
        $this->out = [];

        return $this;
    }

    public function clearError () : ArrayDisplay {
        $this->error = [];

        return $this;
    }

    public function countOut () : int {
        return count($this->out);
    }

    public function countError () : int {
        return count($this->error);
    }

}
