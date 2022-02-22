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
        // The PSR-4 autoloader needs those classes as dependencies:
        require_once __DIR__ . "/PSR4/Autoloader.php";
        require_once __DIR__ . "/PSR4/Solver.php";
        require_once __DIR__ . "/../IO/File.php";
        require_once __DIR__ . "/../Reflection/CodeFile.php";

        self::registerPSR4("Keruald\\OmniTools\\", self::getLibraryPath());
    }

    public static function getLibraryPath () : string {
        return dirname(__DIR__);
    }

}
