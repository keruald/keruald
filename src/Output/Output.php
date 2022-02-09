<?php

namespace Keruald\Reporting\Output;

use Keruald\Reporting\Report;

abstract class Output {

    ///
    /// Properties
    ///

    protected Report $report;

    ///
    /// Abstract methods to implement
    ///

    public abstract function render () : string;

    ///
    /// Constructors
    ///

    public static function for (Report $report) : Output {
        $output = new static();
        $output->report = $report;

        return $output;
    }
}
