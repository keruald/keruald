<?php

namespace Keruald\Reporting\Tests\Output;

use Keruald\Reporting\Output\MarkdownOutput;
use Keruald\Reporting\Report;

use Keruald\Reporting\Tests\WithSampleReport;
use PHPUnit\Framework\TestCase;

class MarkdownOutputTest extends TestCase {

    use WithSampleReport;

    ///
    /// Initialization
    //

    public Report $report;

    protected function setUp () : void {
        $this->report = $this->buildSampleReport();
    }

    ///
    /// Tests
    //

    public function testRender () : void {
        $actual = MarkdownOutput::for($this->report)
            ->render();

        $expected = file_get_contents($this->getDataDir() . "/report.md");

        $this->assertEquals($expected, $actual);
    }
}
