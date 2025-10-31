<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Exceptions\NotImplementedException;
use Keruald\Database\Exceptions\SqlException;
use Keruald\Database\Result\PDODatabaseResult;

use PDO;

abstract class BasePDOPostgreSQLTestCase extends BasePDOTestCase {

    const string DB_NAME = "test_keruald_db";

    public function testQueryScalarWithNonSelectQuery(): void {
        $this->expectException(\LogicException::class);

        $sql = "UPDATE numbers SET number = number * 2";
        $this->db->queryScalar($sql);
    }

    public function testFetchRow(): void {
        $sql = "SELECT 10 UNION SELECT 20 UNION SELECT 30 ORDER BY 1";
        $result = $this->db->query($sql);

        // PostgreSQL uses "?column?" as default column name for unnamed columns
        $this->assertEquals(10, $result->fetchRow()['?column?']);
        $this->assertEquals(20, $result->fetchRow()['?column?']);
        $this->assertEquals(30, $result->fetchRow()['?column?']);

        // Then, we get a null value
        $this->assertEquals(null, $result->fetchRow());
    }

    public function testArrayShapeForFetchRow(): void {
        $sql = "SELECT 10 as score, 50 as \"limit\"";
        $result = $this->db->query($sql);

        $expected = [
            // By column name
            "score" => 10,
            "limit" => 50
        ];

        $this->assertEquals($expected, $result->fetchRow());
    }

    public function testQueryWhenItSucceeds(): void {
        $result = $this->db->query("DELETE FROM numbers");

        $this->assertInstanceOf(PDODatabaseResult::class, $result);
    }

    public function testQueryWhenItFailsWithException(): void {
        $this->expectException(SqlException::class);
        $this->db->query("TRUNCATE not_existing_table");
    }

    public function testQueryWithWrongQueryInLegacyMode(): void {
        $this->db->dontThrowExceptions = true;

        $result = $this->db->query("TRUNCATE not_existing");

        $this->assertFalse($result);
    }

    public function testNextId(): void {
        // Use a transaction to isolate this test
        // Arcanist creates a race condition on this when running from `arc diff`
        $this->db->query("BEGIN");

        try {
            // PostgreSQL uses sequences for auto-increment
            $this->db->query("TRUNCATE numbers RESTART IDENTITY");
            $this->db->query("INSERT INTO numbers (id, number) VALUES (1700, 42742)");
            $this->db->query("INSERT INTO numbers (number) VALUES (666)");

            // Get the last inserted ID
            $lastId = $this->db->nextId();
            $this->assertGreaterThan(0, $lastId);

            $this->db->query("ROLLBACK"); // No need to commit, tests done
        } catch (\Exception $ex) {
            $this->db->query("ROLLBACK");
            $this->fail($ex->getMessage());
        }
    }

    public function testEscapeNotImplemented(): void {
        $this->expectException(NotImplementedException::class);
        $this->expectExceptionMessage('This PDO engine does not support escape for literals');

        $this->db->escape("test'string");
    }


    public function testInOut () : void {
        $this->expectException(NotImplementedException::class);

        $port = 8000;
        $query = $this->db
            ->prepare("CALL define_port(:port);")
            ->bindInOutParameter("port", $port, PDO::PARAM_INT)
            ->query();
    }

}
