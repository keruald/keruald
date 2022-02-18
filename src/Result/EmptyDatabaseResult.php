<?php

namespace Keruald\Database\Result;

use EmptyIterator;
use Traversable;

/**
 * Represents an empty database result, independent of the used database.
 */
class EmptyDatabaseResult extends DatabaseResult {

    ///
    /// DatabaseResult implementation
    ///

    public function numRows () : int {
        return 0;
    }

    public function fetchRow () : ?array {
        return null;
    }

    ///
    /// IteratorAggregate implementation
    ///

    public function getIterator () : Traversable {
        return new EmptyIterator();
    }

}
