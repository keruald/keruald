<?php
declare(strict_types=1);

namespace Keruald\Commands\Tests;

use Keruald\Commands\Command;
use Keruald\Commands\Display\ArrayDisplay;
use Keruald\Commands\ExitCode;
use PHPUnit\Framework\TestCase;

class CommandTest extends TestCase {

    /**
     * @var Command
     */
    private $command;

    /**
     * @var ArrayDisplay
     */
    private $display;


    public function setUp () {
        $this->display = new ArrayDisplay();
        $this->command = new SunsetCommand(1, ["sunset"], $this->display);
    }

    public function testGetArgc () {
        $this->assertEquals(1, $this->command->getArgc());
    }

    public function testGetArgv () {
        $this->assertEquals(["sunset"], $this->command->getArgv());
    }

    public function testGetCommandName() {
        $this->assertEquals("sunset", $this->command->getCommandName());
    }

    public function testDisplayBeforeRun() {
        $this->assertEmpty($this->display->getOut());
        $this->assertEmpty($this->display->getError());
    }

    public function testDisplayAfterRun() {
        $this->command->main();
        $this->assertEquals(1, $this->display->countOut());
        $this->assertEquals(0, $this->display->countError());
    }

    public function testReturnCode () {
        $this->assertEquals(ExitCode::SUCCESS, $this->command->main());
    }

}

