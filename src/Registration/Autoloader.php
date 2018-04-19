<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Registration;

class Autoloader {

    ///
    /// PSR-4
    ///

    public static function registerPSR4 (string $namespace, string $path) : void {
        $loader = new PSR4\Autoloader($namespace, $path);
        $loader->register();
    }

    ///
    /// Include methods
    ///

    public static function tryInclude (string $filename) : void {
        if (!self::canInclude($filename)) {
            return;
        }

        include($filename);
    }

    public static function canInclude (string $filename) : bool {
        return file_exists($filename) && is_readable($filename);
    }

    ///
    /// Methods to register OmniTools library
    ///

    public static function selfRegister () : void {
        self::registerPSR4("Keruald\\OmniTools\\", self::getLibraryPath());
    }

    public static function getLibraryPath () : string {
        return dirname(__DIR__);
    }

}
