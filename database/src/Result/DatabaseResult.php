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

    /**
     * Fetches the value from the first column of the first row in the result.
     *
     * This is typically used for queries that return a single scalar value
     * such as SELECT count(*).
     *
     * @return mixed|null The value, or null if no more rows.
     */
    public function fetchScalar () : mixed {
        $row = $this->fetchRow();

        if (!$row) {
            return null;
        }

        $key = array_key_first($row);
        return $row[$key];
    }
}
