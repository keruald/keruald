<?php

namespace Keruald\Commands\Display;

class OutputDisplay extends Display {

    public function out (string $message) : void {
        echo $message, "\n";
    }

    public function error (string $message) : void {
        fwrite(STDERR, $message . "\n");
    }

}
