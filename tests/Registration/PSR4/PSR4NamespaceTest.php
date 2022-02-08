<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Registration\PSR4;

use Keruald\OmniTools\Registration\PSR4\PSR4Namespace;

use Keruald\OmniTools\Tests\WithData;
use PHPUnit\Framework\TestCase;

class PSR4NamespaceTest extends TestCase {

    use WithData;

    ///
    /// Discovery tests
    ///

    const ALL_CLASSES = [
        "Acme\\SolarSystemLib\\Sun",
        "Acme\\SolarSystemLib\\Planets\\Pluton",
        "Acme\\SolarSystemLib\\Planets\\Inner\\Mercure",
        "Acme\\SolarSystemLib\\Planets\\Inner\\Venus",
    ];

    /**
     * @dataProvider provideClasses
     */
    public function testDiscover (
        string $path, string $prefix, array $expected
    ) : void {
        $ns = new PSR4Namespace($prefix, $this->getDataPath($path));

        $this->assertEquals($expected, $ns->discover());
    }

    public function testDiscoverRecursive () : void {
        $baseDirectory = $this->getDataPath("SolarSystemLib");
        $ns = new PSR4Namespace("Acme\\SolarSystemLib", $baseDirectory);

        $this->assertEquals(self::ALL_CLASSES, $ns->discoverRecursive());
    }

    public function testDiscoverAllClasses () : void {
        $actual = PSR4Namespace::discoverAllClasses(
            "Acme\\SolarSystemLib",
            $this->getDataPath("SolarSystemLib"),
        );

        $this->assertEquals(self::ALL_CLASSES, $actual);

    }

    ///
    /// Data providers
    ///

    public function provideClasses () : iterable {
        // [string $path, string $prefix, string[] $expectedClasses]
        yield ["MockLib", "Acme\\MockLib", [
            "Acme\\MockLib\\Bar",
            "Acme\\MockLib\\Foo",
        ]];

        yield ["SolarSystemLib", "Acme\\SolarSystemLib", [
            "Acme\\SolarSystemLib\\Sun",
        ]];

        yield ["SolarSystemLib/Planets", "Acme\\SolarSystemLib\\Planets", [
            "Acme\\SolarSystemLib\\Planets\\Pluton",
        ]];

        yield [
            "SolarSystemLib/Planets/Inner",
            "Acme\\SolarSystemLib\\Planets\\Inner",
            [
                "Acme\\SolarSystemLib\\Planets\\Inner\\Mercure",
                "Acme\\SolarSystemLib\\Planets\\Inner\\Venus",
            ]
        ];

        yield ["NotExisting", "AnyPrefix", []];
    }
}
