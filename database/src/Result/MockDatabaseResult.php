<?php

namespace Keruald\Database\Result;

use ArrayIterator;
use Traversable;

class MockDatabaseResult extends DatabaseResult {

    ///
    /// Constructor
    ///

    public function __construct (private readonly array $results) {
    }

    ///
    /// IteratorAggregate
    ///

    public function getIterator () : Traversable {
        return new ArrayIterator($this->results);
    }

    ///
    /// DatabaseResult
    ///

    public function numRows () : int {
        return count($this->results);
    }

    public function fetchRow () : ?array {
        static $position = 0;

        if ($position < $this->numRows()) {
            return $this->results[$position];
        }

        return null;
    }
}
