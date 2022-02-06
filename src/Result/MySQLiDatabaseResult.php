<?php

namespace Keruald\Database\Result;

use mysqli_result;
use Traversable;

class MySQLiDatabaseResult extends DatabaseResult {

    ///
    /// Constructor
    ///

    public function __construct (
        private mysqli_result $result,
        private int $resultType = MYSQLI_BOTH
    ) { }

    ///
    /// DatabaseResult implementation
    ///

    public function numRows () : int {
        return $this->result->num_rows;
    }

    public function fetchRow () : ?array {
        return $this->result->fetch_array($this->resultType);
    }

    ///
    /// IteratorAggregate implementation
    ///

    public function getIterator () : Traversable {
        while ($row = $this->fetchRow()) {
            yield $row;
        }
    }

}
