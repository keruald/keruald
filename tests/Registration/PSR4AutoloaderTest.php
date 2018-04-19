<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Registration;

use Keruald\OmniTools\Registration\PSR4\Solver;
use PHPUnit\Framework\TestCase;

class PSR4AutoloaderTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @dataProvider providePaths
     */
    public function testGetPathFor (string $class, string $expected) : void {
        $this->assertEquals($expected, Solver::getPathFor($class));
    }

    ///
    /// Data provider
    ///

    public function providePaths () : iterable {
        // Example from PSR-4 canonical document
        yield ['File_Writer', 'File_Writer.php'];
        yield ['Response\Status', 'Response/Status.php'];
        yield ['Request', 'Request.php'];
    }


}
