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
    /// Methods to register OmniTools library
    ///

    public static function selfRegister () : void {
        self::registerPSR4("Keruald\\OmniTools\\", self::getLibraryPath());
    }

    public static function getLibraryPath () : string {
        return dirname(__DIR__);
    }

}
