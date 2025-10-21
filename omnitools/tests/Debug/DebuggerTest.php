<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Debug;

use Keruald\OmniTools\Debug\Debugger;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DebuggerTest extends TestCase {

    ///
    /// Unit tests
    ///

    public function testRegister () {
        $this->assertTestSuiteStateIsValid();

        Debugger::register();

        $this->assertTrue(function_exists("dprint_r"));
        $this->assertTrue(defined("SQL_ERROR"));
    }

    private function assertTestSuiteStateIsValid() : void {
        $this->assertFalse(
            function_exists("dprint_r"),
            "Configure the test suite so dprint_r isn't in global space first."
        );
    }

    ///
    /// Integration tests
    ///
    #[DataProvider('provideDebuggerScripts')]
    public function testDebuggerScript ($script, $message) : void {
        $this->assertProgramMatchesOutput($script, $message);
    }

    private function assertProgramMatchesOutput(string $script, string $message = "") : void {
        $filename = __DIR__ . "/testers/$script";

        $expected = file_get_contents($filename . ".txt");
        $actual = `php $filename.php`;

        $this->assertSame($expected, $actual, $message);
    }

    public static function provideDebuggerScripts () : iterable {
        yield ["dump_integer", "Can't dump a variable"];
        yield ["dump_array", "Can't dump an array"];
        yield ["dump_object", "Can't dump an object"];
        yield ["check_die", "printVariableAndDie doesn't die"];
    }

}
