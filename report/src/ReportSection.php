<?php

namespace Keruald\Reporting;

class ReportSection {

    public function __construct (
        public string $title,

        /**
         * @var ReportEntry[]
         */
        public array $entries = [],
    ) { }

    public function push (string $title, string $text) : void {
        $this->entries[] = new ReportEntry($title, $text);
    }

    public function isEmpty () : bool {
        return count($this->entries) === 0;
    }

}
