<?php

namespace Keruald\Database\Tests\Result;

use Keruald\Database\Result\EmptyDatabaseResult;
use PHPUnit\Framework\TestCase;

class EmptyDatabaseResultTest extends TestCase {

    private EmptyDatabaseResult $result;

    protected function setUp () : void {
        $this->result = new EmptyDatabaseResult();
    }

    public function testNumRows () : void {
        $this->assertSame(0, $this->result->numRows());
    }

    public function testFetchRow () : void {
        $this->assertEmpty($this->result->fetchRow());
    }

    public function testFetchScalar () : void {
        $this->assertNull($this->result->fetchScalar());
    }

    public function testGetIterator () : void {
        $actual = iterator_to_array($this->result->getIterator());

        $this->assertSame([], $actual);
    }

}
