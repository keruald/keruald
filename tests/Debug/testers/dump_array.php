<?php

use Keruald\OmniTools\Debug\Debugger;

// Include Debugger class file
$libDirectory = dirname(__DIR__, 3);
require $libDirectory . "/src/Debug/Debugger.php";

Debugger::printVariableAndDie(["foo" => "bar"]);
echo "The script didn't die as expected.";
