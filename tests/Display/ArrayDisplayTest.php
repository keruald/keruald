<?php
declare(strict_types=1);

namespace Keruald\Commands\Tests\Display;

use Keruald\Commands\Display\ArrayDisplay;
use PHPUnit\Framework\TestCase;

class ArrayDisplayTest extends TestCase {

    /**
     * @var ArrayDisplay
     */
    private $display;

    public function setUp () : void {
        $this->display = new ArrayDisplay;
    }

    public function testOut () : void {
        $this->display->out("Hello world!");

        $this->assertEquals(["Hello world!"], $this->display->getOut());
        $this->assertEquals([], $this->display->getError());

        $this->assertEquals(1, $this->display->countOut());
        $this->assertEquals(0, $this->display->countError());
    }

    public function testClearOut () : void {
        $this->display->out("Lorem");
        $this->display->out("Ipsum");
        $this->display->out("Dolor");
        $this->display->clearOut();

        $this->assertEquals(0, $this->display->countOut());
    }

    public function testClearError () : void {
        $this->display->error("Lorem");
        $this->display->error("Ipsum");
        $this->display->error("Dolor");
        $this->display->clearError();

        $this->assertEquals(0, $this->display->countError());
    }

    public function testCountOut () : void {
        $this->display->out("Lorem");
        $this->display->out("Ipsum");
        $this->display->out("Dolor");

        $this->assertEquals(3, $this->display->countOut());
    }

    public function testCountError () : void {
        $this->display->error("Lorem");
        $this->display->error("Ipsum");
        $this->display->error("Dolor");

        $this->assertEquals(3, $this->display->countError());
    }


}
