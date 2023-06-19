<?php

namespace Keruald\Database\Tests\Result;

use Keruald\Database\Result\MockDatabaseResult;
use PHPUnit\Framework\TestCase;

class MockDatabaseResultTest extends TestCase {

    const RESULT = [
        [ "name" => "strawberry", "color" => "red" ],
        [ "name" => "blueberry", "color" => "violet" ],
    ];

    private MockDatabaseResult $result;

    protected function setUp () : void {
        $this->result = new MockDatabaseResult(self::RESULT);
    }

    public function testFetchRow () {
        $this->assertEquals(
            [ "name" => "strawberry", "color" => "red" ],
            $this->result->fetchRow(),
        );
    }

    public function testNumRows () {
        $this->assertEquals(2, $this->result->numRows());
    }

    public function testGetIterator () {
        $i = 0;
        foreach ($this->result as $row) {
            $this->assertEquals(self::RESULT[$i], $row);
            $i++;
        }
    }

}
