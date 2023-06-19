<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\MockDatabaseEngine;

use Keruald\Database\Result\DatabaseResult;
use PHPUnit\Framework\TestCase;

class MockDatabaseEngineTest extends TestCase {

    private MockDatabaseEngine $db;

    const QUERY = "SELECT name, color FROM fruits";

    const RESULT = [
        [ "name" => "strawberry", "color" => "red" ],
        [ "name" => "blueberry", "color" => "violet" ],
    ];

    protected function setUp () : void {
        $queries = [
            self::QUERY => self::RESULT,
        ];

        $this->db = (new MockDatabaseEngine())
            ->withQueries($queries);
    }

    public function testNumRowsWithoutRequest () {
        $this->assertEquals(0, $this->db->numRows());
    }

    public function testNumRowsWithRequest () {
        $result = $this->db->query(self::QUERY);

        $this->assertEquals(2, $this->db->numRows($result));
    }

    public function testQuery () {
        $result = $this->db->query(self::QUERY);

        $this->assertInstanceOf(DatabaseResult::class, $result);
        $this->assertEquals(self::RESULT[0], $result->fetchRow());
    }

    public function testLoad () {
        $db = MockDatabaseEngine::load([]);

        $this->assertInstanceOf(MockDatabaseEngine::class, $db);
    }

    public function testFetchRow () {
        $result = $this->db->query(self::QUERY);

        $this->assertEquals(self::RESULT[0], $this->db->fetchRow($result));
    }

    public function testWithQueriesFollowsChainingPattern () {
        $queries = [
            self::QUERY => self::RESULT,
        ];

        $db = new MockDatabaseEngine();
        $db = $db->withQueries($queries);

        $this->assertInstanceOf(MockDatabaseEngine::class, $db);
    }
}
