<?php

namespace Keruald\Commands\Tests;

use Keruald\Commands\Command;
use Keruald\Commands\ExitCode;

class SunsetCommand extends Command {

    public function main () : int {
        $this->display->out(self::computeTodaySunsetTime());

        return ExitCode::SUCCESS;
    }

    private static function computeTodaySunsetTime () {
        return date_sunset(time());
    }

}
