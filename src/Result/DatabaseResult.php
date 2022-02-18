<?php

namespace Keruald\Database\Result;

use IteratorAggregate;

/**
 * Represents a database result
 */
abstract class DatabaseResult implements IteratorAggregate {

    /**
     * Gets number of rows in result
     */
    public abstract function numRows () : int;

    /**
     * Fetches a row of the result
     * @return array|null An array if there is still a row to read; null if not.
     */
    public abstract function fetchRow () : ?array;
}
