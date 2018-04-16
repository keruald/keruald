<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Registration;

class Autoloader {

    ///
    /// PSR-4
    ///

    public static function registerPSR4 (string $namespace, string $path) : void {
        spl_autoload_register(function ($class) use ($namespace, $path) {
            $len = strlen($namespace);

            if (substr($class, 0, $len) === $namespace) {
                $classPath = Autoloader::getPathFor(substr($class, $len));
                include $path . '/' . $classPath;
            }
        });
    }

    public static function getPathFor (string $name) : string {
        return str_replace("\\", "/", $name) . '.php';
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
