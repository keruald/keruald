<?php

namespace Keruald\Reporting;

class Report {

    public function __construct (
        public string $title,

        /**
         * @var ReportSection[]
         */
        public array $sections = [],

        /**
         * @var array<string, mixed>
         */
        public array $properties = [],
    ) { }

    public function push (ReportSection $section) : void {
        $this->sections[] = $section;
    }

    public function pushIfNotEmpty (ReportSection $report) : void {
        if (!$report->isEmpty()) {
            $this->push($report);
        }
    }

}
