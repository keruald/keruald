<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Database;
use Keruald\Database\Engines\BlackholeEngine;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class BlackholeEngineTest extends TestCase {

    private BlackholeEngine $db;

    protected function setUp () : void {
        $this->db = new BlackholeEngine();
    }

    public function testGetUnderlyingDriver () {
        $this->assertNull($this->db->getUnderlyingDriver());
    }

    public function testQuery () {
        $this->assertTrue($this->db->query(""));
    }

    public function testNextId () {
        $this->assertEquals(0, $this->db->nextId());
    }

    public function testIsExistingTable () {
        $this->assertTrue($this->db->isExistingTable("quux", "quuxians"));
    }

    public static function provideStringsToEscape () : iterable {
        yield ["Lorem ipsum"];
        yield [""];
        yield ["\\\\ \n"];
    }

    #[DataProvider('provideStringsToEscape')]
    public function testEscape ($string) {
        $this->assertEquals($string, $this->db->escape($string));
    }

    public function testLoad () {
        $config = [
            'engine' => BlackholeEngine::class,
        ];
        $engine = Database::load($config);

        $this->assertInstanceOf(BlackholeEngine::class, $engine);
    }

    public function testCountAffectedRows () {
        $this->assertEquals(0, $this->db->nextId());
    }
}
