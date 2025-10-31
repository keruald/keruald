<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\PDOEngine;
use Keruald\Database\Exceptions\SqlException;
use Keruald\Database\Query\PDOQuery;
use Keruald\Database\Result\PDODatabaseResult;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use PDO;
use PDOStatement;

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

    ///
    /// Integration tests for PDOQuery
    ///

    public function testPrepare(): void {
        $sql = "SELECT :word";

        $query = $this->db->prepare($sql);
        $this->assertInstanceOf(PDOQuery::class, $query);

        $result = $query
            ->with("word", "foo")
            ->query();
        $this->assertInstanceOf(PDODatabaseResult::class, $result);

        $row = $result->fetchRow();
        $this->assertContains("foo", $row);
    }

    public function testWithValue(): void {
        $query = $this->db
            ->prepare("SELECT :word")
            ->withvalue("word", "foo");

         $this->assertEquals("foo", $query->query()->fetchScalar());

    }

    public function testWithValueWithVariableChange(): void {
        $word = "foo";

        $query = $this->db
            ->prepare("SELECT :word")
            ->withvalue("word", $word);

         $word = "bar";

         $this->assertEquals("foo", $query->query()->fetchScalar());

    }

    public function testWithIndexedValue(): void {
        $query = $this->db
            ->prepare("SELECT ?")
            ->withIndexedValue(1, "foo");

        $this->assertEquals("foo", $query->query()->fetchScalar());
    }

    public function testBind(): void {
        $word = "foo";

        $query = $this->db
            ->prepare("SELECT :word")
            ->bind("word", $word);

         $this->assertEquals("foo", $query->query()->fetchScalar());

    }

    public function testBindWithVariableChange(): void {
        $word = "foo";

        $query = $this->db
            ->prepare("SELECT :word")
            ->bind("word", $word);

         $word = "bar";

         $this->assertEquals("bar", $query->query()->fetchScalar());
    }


    public static function provideFetchModeAndScalarResults(): iterable {
        yield "PDO::FETCH_ASSOC" => [
            PDO::FETCH_ASSOC,
            ["?column?" => "foo"],
        ];

        yield "PDO::FETCH_NUM" => [
            PDO::FETCH_NUM,
            [0 => "foo"],
        ];

        yield "PDO::FETCH_BOTH" => [
            PDO::FETCH_BOTH,
            [0 => "foo", "?column?" => "foo"],
        ];
    }

    #[DataProvider("provideFetchModeAndScalarResults")]
    public function testFetchMode($mode, $expected): void {
        $actual = $this->db
            ->prepare("SELECT :word")
            ->with("word", "foo")
            ->withFetchMode($mode)
            ->query()
            ->fetchRow();

        $this->assertEquals($expected, $actual);
    }

    public function testGetUnderlyingStatement () : void {
        $query = $this->db
            ->prepare("SELECT :word");

        $this->assertInstanceOf(PDOStatement::class, $query->getUnderlyingStatement());
    }

    public function testToString () : void {
        $query = $this->db
            ->prepare("SELECT :word");

        $this->assertEquals("SELECT :word", (string)$query);
    }

    public function testToStringIsInvariant () : void {
        $query = $this->db
            ->prepare("SELECT :word")
            ->with("word", "foo");

        $this->assertEquals("SELECT :word", (string)$query);
    }

    public function testQueryWithError () : void {
        $this->expectException(SqlException::class);

        $sql = "SELECT * FROM nonexisting";
        $result = $this->db->prepare($sql)->query();

        $this->assertNull($result);
    }

    public function testQueryWithErrorWhenExceptionsAreDisabled () : void {
        $this->db->dontThrowExceptions = true;

        $sql = "SELECT * FROM nonexisting";
        $result = $this->db->prepare($sql)->query();

        $this->assertNull($result);
    }

}
