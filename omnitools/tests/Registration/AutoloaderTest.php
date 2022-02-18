<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Registration;

use Keruald\OmniTools\Registration\Autoloader;
use Keruald\OmniTools\Tests\WithData;
use PHPUnit\Framework\TestCase;

use Acme\MockLib\Foo; // a mock class in a mock namespace to test autoload.

class AutoloaderTest extends TestCase {

    use WithData;

    public function testRegisterPSR4 () : void {
        $class = Foo::class;
        $this->assertFalse(
            class_exists($class),
            "Please reconfigure the test suite not to include the $class class."
        );

        Autoloader::registerPSR4("Acme\\MockLib\\", $this->getDataPath("MockLib"));
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

}
