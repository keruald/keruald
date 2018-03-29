<?php
declare(strict_types=1);

namespace Keruald\Commands\Tests\Display;

use Keruald\Commands\Display\OutputDisplay;
use PHPUnit\Framework\TestCase;

class OutputDisplayTest extends TestCase {

    /**
     * @var OutputDisplay
     */
    private $display;

    public function setUp () {
        $this->display = new OutputDisplay;
    }

    public function testOut () {
        $this->expectOutputString("Hello world!\n");
        $this->display->out("Hello world!");
    }

}
