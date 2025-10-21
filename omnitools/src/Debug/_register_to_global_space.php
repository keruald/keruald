<?php

/* This code is intentionally left in the global namespace. */

use Keruald\OmniTools\Debug\Debugger;

const SQL_ERROR = 65;
const HACK_ERROR = 99;
const GENERAL_ERROR = 117;

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
