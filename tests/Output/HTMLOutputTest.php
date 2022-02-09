<?php

namespace Keruald\Reporting\Tests\Output;

use Keruald\Reporting\Output\HTMLOutput;
use Keruald\Reporting\Report;

use Keruald\Reporting\Tests\WithSampleReport;
use PHPUnit\Framework\TestCase;

class HTMLOutputTest extends TestCase {

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
        $actual = HTMLOutput::for($this->report)
                                ->render();

        $expected = file_get_contents($this->getDataDir() . "/report.html");

        $this->assertEquals($expected, $actual);
    }
}
