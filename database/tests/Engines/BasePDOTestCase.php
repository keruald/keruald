<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\PDOEngine;

use Keruald\Database\Exceptions\SqlException;
use PHPUnit\Framework\TestCase;

abstract class BasePDOTestCase extends TestCase {

    protected PDOEngine $db;

    protected static abstract function buildEngine () : PDOEngine;

    protected function setUp (): void {
        $this->db = static::buildEngine();
    }

    public function testQueryScalar(): void {
        $sql = "SELECT 1+1";
        $this->assertEquals(2, $this->db->queryScalar($sql));
    }

    public function testQueryScalarWithoutQuery(): void {
        $this->assertEquals("", $this->db->queryScalar(""));
    }

    public function testQueryScalarWithWrongQuery(): void {
        $this->expectException(SqlException::class);

        $sql = "DELETE FROM nonexisting";
        $this->db->queryScalar($sql);
    }
}
