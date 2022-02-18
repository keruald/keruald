<?php

use Keruald\OmniTools\Debug\Debugger;
use Acme\Counter;

// Include Debugger class file
$libDirectory = dirname(__DIR__, 3);
require $libDirectory . "/src/Debug/Debugger.php";
require "Counter.php";

Debugger::printVariableAndDie(new Counter);
