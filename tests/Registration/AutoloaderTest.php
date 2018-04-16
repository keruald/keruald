<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Registration;

use Keruald\OmniTools\Registration\Autoloader;
use PHPUnit\Framework\TestCase;

use Acme\MockLib\Foo; // a mock class in a mock namespace to test autoload.

class AutoloaderTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @dataProvider providePaths
     */
    public function testGetPathFor (string $class, string $expected) : void {
        $this->assertEquals($expected, Autoloader::getPathFor($class));
    }

    public function testRegisterPSR4 () : void {
        $class = Foo::class;
        $this->assertFalse(
            class_exists($class),
            "Please reconfigure the test suite not to include the $class class."
        );

        Autoloader::registerPSR4("Acme\\MockLib\\", __DIR__ . "/MockLib");
        $this->assertTrue(class_exists($class));
    }

    public function testGetLibraryPath () : void {
        $this->assertStringStartsWith(
            dirname(Autoloader::getLibraryPath()),  // lib is in <root>/src
            __DIR__                                 // we are in <root>/tests/…
        );
    }

    public function testRegister () : void {
        $count = count(spl_autoload_functions());

        Autoloader::selfRegister();

        $this->assertEquals(++$count, count(spl_autoload_functions()));
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
