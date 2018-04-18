<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Debug;

class Debugger {

    /**
     * Prints human-readable information about a variable, wrapped in a <pre> block
     *
     * @param mixed $variable the variable to dump
     */
    public static function printVariable ($variable) : void {
        echo "<pre class='debug'>";
        print_r($variable);
        echo "</pre>";
    }

    public static function printVariableAndDie ($variable) : void {
        static::printVariable($variable);
        die;
    }

    ///
    /// Comfort debug helper to register debug method in global space
    ///

    public static function register () : void {
        require_once '_register_to_global_space.php';
    }

}
