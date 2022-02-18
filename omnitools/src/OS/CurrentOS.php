<?php
declare(strict_types=1);

namespace Keruald\OmniTools\OS;

class CurrentOS {

    public static function isWindows () : bool {
        return PHP_OS === 'CYGWIN' || self::isPureWindows();
    }

    public static function isPureWindows () : bool {
        return strtoupper(substr(PHP_OS, 0, 3)) === 'WIN';
    }

}
