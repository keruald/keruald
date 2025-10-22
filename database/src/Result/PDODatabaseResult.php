<?php

namespace Keruald\Database\Result;

use PDO;
use PDOStatement;

use Traversable;

class PDODatabaseResult extends DatabaseResult {

    public function __construct (
        private readonly PDOStatement $statement,
        private readonly int $fetchMode = PDO::FETCH_ASSOC,
    ) { }

    ///
    /// DatabaseResult implementation
    ///

    public function numRows () : int {
        return $this->statement->rowCount();
    }

    public function fetchRow () : ?array {
        $row = $this->statement->fetch($this->fetchMode);

        if ($row === false) {
            return null;
        }

        return $row;
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
