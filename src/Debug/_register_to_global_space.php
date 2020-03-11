<?php

/* This code is intentionally left in the global namespace. */

use Keruald\OmniTools\Debug\Debugger;

if (!function_exists("dprint_r")) {
    function dprint_r ($variable) {
        Debugger::printVariable($variable);
    }
}

if (!function_exists("dieprint_r")) {
    function dieprint_r ($variable) {
        Debugger::printVariableAndDie($variable);
    }
}
