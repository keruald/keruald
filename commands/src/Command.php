<?php

namespace Keruald\Commands;

use Keruald\Commands\Display\Display;
use Keruald\Commands\Display\OutputDisplay;

abstract class Command {

    protected Display $display;

    public function __construct (
        public int $argc,
        public array $argv,
        Display $display = null
    ) {
        if ($display === null) {
            $display = self::getDefaultDisplay();
        }
        $this->display = $display;
    }

    public static function run (int $argc, array $argv) : int {
        $command = new static($argc, $argv);

        return $command->main();
    }

    ///
    /// Getters and setters
    ///

    /**
     * @deprecated Use directly Command::$argc
     */
    public function getArgc () : int {
        return $this->argc;
    }

    /**
     * @deprecated Use directly Command::$argc
     */
    public function setArgc (int $argc) : Command {
        $this->argc = $argc;

        return $this;
    }

    /**
     * @deprecated Use directly Command::$argv
     */
    public function getArgv () : array {
        return $this->argv;
    }


    /**
     * @deprecated Use directly Command::$argv
     */
    public function setArgv (array $argv) : Command {
        $this->argv = $argv;

        return $this;
    }

    public function getCommandName () : string {
        return $this->argv[0] ?? "";
    }

    public function getDisplay () : Display {
        return $this->display;
    }

    public function setDisplay (Display $display) : Command {
        $this->display = $display;

        return $this;
    }

    ///
    /// Helper methods
    ///

    private static function getDefaultDisplay () : Display {
        return new OutputDisplay();
    }

    ///
    /// Methods to implement
    ///

    public abstract function main () : int;

}
