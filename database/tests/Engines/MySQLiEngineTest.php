<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\MySQLiEngine;
use Keruald\Database\Exceptions\EngineSetupException;
use Keruald\Database\Exceptions\SqlException;

use LogicException;
use PHPUnit\Framework\TestCase;

class MySQLiEngineTest extends TestCase {

    private MySQLiEngine $db;

    const DB_NAME = "test_keruald_db";

    protected function setUp (): void {
        $this->db = new MySQLiEngine('localhost', '', '', self::DB_NAME);
    }

    public function testLoad () {
        $instance = MySQLiEngine::load([
            'host' => 'localhost',
            'username' => '',
            'password' => '',
            'database' => self::DB_NAME,
        ]);

        $this->assertInstanceOf("mysqli", $instance->getUnderlyingDriver());
    }

    public function testLoadWithWrongPassword () {
        $this->expectException(EngineSetupException::class);

        $instance = MySQLiEngine::load([
            'host' => 'localhost',
            'username' => 'notexisting',
            'password' => 'notexistingeither',
            'database' => self::DB_NAME,
        ]);
    }

    public function testQueryScalar () {
        $sql = "SELECT 1+1";
        $this->assertEquals(2, $this->db->queryScalar($sql));
    }

    public function testQueryScalarWithoutQuery () {
        $this->assertEquals("", $this->db->queryScalar(""));
    }

    public function testQueryScalarWithWrongQuery () {
        $this->expectException(SqlException::class);

        $sql = "DELETE FROM nonexisting";
        $this->db->queryScalar($sql);
    }

    public function testQueryScalarWithNonSelectQuery () {
        $this->expectException(LogicException::class);

        $sql = "UPDATE numbers SET number = number * 2";
        $this->db->queryScalar($sql);
    }

    public function testSetCharset () {
        $expected = "binary";
        $this->db->setCharset($expected);

        $sql = "SELECT @@SESSION.character_set_connection";
        $actual = $this->db->queryScalar($sql);

        $this->assertEquals($expected, $actual);
    }

    public function testFetchRow () {
        $sql = "SELECT 10 UNION SELECT 20 UNION SELECT 30";
        $result = $this->db->query($sql);

        // First, we get associative arrays like [0 => 10,     10u => 10]
        //                                        ^ position   ^ column name
        $this->assertEquals(10, $this->db->fetchRow($result)[10]);
        $this->assertEquals(20, $this->db->fetchRow($result)[10]);
        $this->assertEquals(30, $this->db->fetchRow($result)[10]);

        // Then, we get a null value
        $this->assertEquals(null, $this->db->fetchRow($result));
    }

    public function testArrayShapeForFetchRow () {
        $sql = "SELECT 10 as score, 50 as `limit`";
        $result = $this->db->query($sql);

        $expected = [
            // By column name
            "score" => 10,
            "limit" => 50
        ];

        $this->assertEquals($expected, $this->db->fetchRow($result));
    }

    public function testArrayShapeForFetchRowWithFetchModeBoth () {
        $sql = "SELECT 10 as score, 50 as `limit`";
        $this->db->setFetchMode(MYSQLI_BOTH);
        $result = $this->db->query($sql);

        $expected = [
            // By position
            0 => 10,
            1 => 50,

            // By column name
            "score" => 10,
            "limit" => 50
        ];

        $this->assertEquals($expected, $this->db->fetchRow($result));
    }

    public function testArrayShapeForFetchRowWithFetchModeEnum () {
        $sql = "SELECT 10 as score, 50 as `limit`";
        $this->db->setFetchMode(MYSQLI_NUM);
        $result = $this->db->query($sql);

        $expected = [
            // By position
            0 => 10,
            1 => 50,
        ];

        $this->assertEquals($expected, $this->db->fetchRow($result));
    }

    public function testQueryWhenItSucceeds () {
        $result = $this->db->query("DELETE FROM numbers");

        $this->assertTrue($result);
    }

    public function testQueryWhenItFailsWithoutException () {
        mysqli_report(MYSQLI_REPORT_OFF);

        $result = $this->db->query("TRUNCATE not_existing");

        $this->assertFalse($result);
    }

    public function testQueryWhenItFailsWithException () {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

        $this->expectException(SqlException::class);
        $this->db->query("TRUNCATE not_existing_table");
    }

    public function testQueryWithWrongQueryInLegacyMode () {
        mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
        $this->db->dontThrowExceptions = true;

        $result = $this->db->query("TRUNCATE not_existing");

        $this->assertFalse($result);
    }

    public function testNextId () {
        $this->db->query("TRUNCATE numbers");
        $this->db->query("INSERT INTO numbers VALUES (1700, 42742)");
        $this->db->query("INSERT INTO numbers (number) VALUES (666)");

        $this->assertSame(1701, $this->db->nextId());
    }

    public function testEscape () {
        $this->assertEquals("foo\')", $this->db->escape("foo')"));
    }

    public function testGetUnderlyingDriver () {
        $this->assertInstanceOf("mysqli", $this->db->getUnderlyingDriver());
    }

    public function testNumRowsForSelect () {
        $sql = "SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4";
        $result = $this->db->query($sql);

        $this->assertSame(4, $this->db->numRows($result));
    }

    public function testNumRowsForInsert () {
        $sql = "INSERT INTO numbers (number) VALUES (1), (2), (3), (4), (5)";
        $result = $this->db->query($sql);

        $this->assertSame(5, $this->db->numRows($result));
    }

    public function testError () {
        $expected = [
            "code" => 1146,
            "message" => "Table 'test_keruald_db.not_existing' doesn't exist",
        ];

        mysqli_report(MYSQLI_REPORT_OFF);
        $this->db->query("TRUNCATE not_existing");

        $this->assertEquals($expected, $this->db->error());
    }

    public function testIsView () {
        $this->assertTrue($this->db->isView(self::DB_NAME, "ships_count"));
    }

    public function testIsViewWhenItIsTable () {
        $this->assertFalse($this->db->isView(self::DB_NAME, "ships"));
    }

    public function testIsViewWhenNotExisting () {
        $this->assertFalse($this->db->isView(self::DB_NAME, "notexisting"));
    }

    public function testIsExisting () {
        $this->assertTrue($this->db->isExistingTable(self::DB_NAME, "ships"));
    }

    public function testIsExistingWithView () {
        $this->assertTrue($this->db->isExistingTable(
            self::DB_NAME, "ships_count")
        );
    }

    public function testIsExistingWhenNotExisting () {
        $this->assertFalse($this->db->isExistingTable(
            self::DB_NAME, "notexisting")
        );
    }

}
