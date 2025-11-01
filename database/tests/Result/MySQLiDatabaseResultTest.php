<?php

namespace Keruald\Database\Tests\Result;

use Keruald\Database\Engines\MySQLiEngine;
use Keruald\Database\Result\MySQLiDatabaseResult;
use PHPUnit\Framework\TestCase;

class MySQLiDatabaseResultTest extends TestCase {

    private MySQLiDatabaseResult $result;

    protected function setUp () : void {
        $db = new MySQLiEngine('localhost', '', '', 'test_keruald_db');
        $db->setFetchMode(MYSQLI_BOTH);

        $sql = "SELECT id, name, category FROM ships";
        $this->result = $db->query($sql);
    }

    public function provideExpectedData () : array {
        $data = [
            // MYSQLI_NUM data
            ["1", "So Much For Subtlety", "GSV"],
            ["2", "Unfortunate Conflict Of Evidence", "GSV"],
            ["3", "Just Read The Instructions", "GCU"],
            ["4", "Just Another Victim Of The Ambient Morality", "GCU"],
        ];

        return array_map(function ($row) {
            // MYSQLI_ASSOC data
            return $row + [
                "id" => $row[0],
                "name" => $row[1],
                "category" => $row[2],
            ];
        }, $data);
    }

    public function testGetIterator () {
        $actual = iterator_to_array($this->result->getIterator());

        $this->assertEquals($this->provideExpectedData(), $actual);
    }

    public function testFetchRow () {
        $expected = $this->provideExpectedData()[0];

        $this->assertEquals($expected, $this->result->fetchRow());
    }

    public function testFetchScalar () {
        $this->assertEquals("1", $this->result->fetchScalar());
    }

    public function testNumRows () {
        $this->assertEquals(4, $this->result->numRows());
    }
}
