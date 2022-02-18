<?php

namespace Keruald\Commands\Tests;

use Keruald\Commands\Command;
use Keruald\Commands\ExitCode;

class HelloWorldCommand extends Command {

    public function main () : int {
        $this->display->out("Hello world!");

        return ExitCode::SUCCESS;
    }

}
