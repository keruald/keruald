<?php

namespace Keruald\Database;

use Keruald\Database\Exceptions\SqlException;

use BadMethodCallException;
use Keruald\Database\Result\DatabaseResult;
use LogicException;

abstract class DatabaseEngine {

    ///
    /// Traits
    ///

    use WithLegacyMethods;

    ///
    /// Methods the specific engine need to implement to access database
    ///

    public abstract function escape (string $expression) : string;

    public abstract function query (string $query);

    public abstract function nextId () : int|string;

    public abstract function countAffectedRows () : int;

    protected abstract function getExceptionContext (): array;

    /**
     * Determines if the specified table or view exists.
     */
    public abstract function isExistingTable (
        string $database, string $table
    ) : bool;

    ///
    /// Engine mechanics
    ///

    public abstract static function load (array $config): DatabaseEngine;

    public abstract function getUnderlyingDriver () : mixed;

    ///
    /// Helpers we can use across all engines
    ///

    /**
     * Runs a query, then returns the first scalar element of result,
     * ie the element in the first column of the first row.
     *
     * This is intended for queries with only one scalar result like
     * 'SELECT count(*) FROM …' or 'SELECT value WHERE unique_key = …'.
     *
     * @param string $query The query to execute
     * @return string the scalar result
     * @throws SqlException
     */
    public function queryScalar (string $query = '') : string {
        if ($query === '') {
            //No query, no value
            return '';
        }

        $result = $this->query($query);

        // If legacy mode is enabled, we have a MySQL error here.
        if ($result === false) {
            throw SqlException::fromQuery($query, $this->getExceptionContext());
        }

        // Ensure the query is SELECT / SHOW / DESCRIBE / EXPLAIN,
        // so we have a scalar result to actually return.
        //
        // That allows to detect bugs where queryScalar() is used
        // with the intent to fetch metadata information,
        // e.g. the amount of rows updated or deleted.
        if (is_bool($result)) {
            throw new LogicException("The queryScalar method is intended
            to be used with SELECT queries and assimilated");
        }

        // Fetches first row of the query, and return the first element
        // If there isn't any result row, returns an empty string.
        $row = $result->fetchRow();
        if ($row === null) {
            return "";
        }

        $key = array_key_first($row);
        return match ($key) {
            null => "",
            default => $row[$key],
        };
    }

    ///
    /// Compatibility with legacy code
    ///

    /**
     * @var bool Don't throw exceptions if a query doesn't succeed
     * @deprecated Replace `if (!$result = $db->query(…))` by an error handler
     */
    public bool $dontThrowExceptions = false;

    /**
     * Gets the number of rows affected or returned by a query.
     *
     * @return int the number of rows affected (delete/insert/update)
     *             or the number of rows in query result
     * @deprecated Use $result->numRows or $db->countAffectedRows();
     */
    public function numRows (DatabaseResult|bool $result = false) : int {
        if ($result instanceof DatabaseResult) {
            return $result->numRows();
        }

        return $this->countAffectedRows();
    }

    /**
     * Fetches a row from the query result.
     *
     * @param DatabaseResult $result The query result
     * @return array|null An associative array with the database result,
     *                    or null if no more result is available.
     */
    public function fetchRow (DatabaseResult $result) : ?array {
        return $result->fetchRow();
    }

    /**
     * Allows the legacy use of sql_query, sql_fetchrow, sql_escape, etc.
     *
     * @throws BadMethodCallException when the method name doesn't exist.
     * @deprecated
     */
    public function __call (string $name, array $arguments) {
        if (str_starts_with($name, 'sql_')) {
            return $this->callByLegacyMethodName($name, $arguments);
        }

        $className = get_class($this);
        throw new BadMethodCallException(
            "Method doesn't exist: $className::$name"
        );
    }


    ///
    /// Events
    ///

    public const string EVENTS_PROPAGATION_CLASS = "Keruald\\OmniTools\\Events\\Propagation";

    /**
     * @event CantConnectToHost Functions to call when it's not possible to connect to the database host
     * @eventparam Database $db The current database instance
     */
    public array $cantConnectToHostEvents = [];

    /**
     * @event QueryError Functions to call when a query fails.
     * @eventparam Database $db The current database instance
     * @eventparam string $query The failed query
     * @eventparam DatabaseException $ex The exception describing the query error
     */
    public array $queryErrorEvents = [];

    /**
     * Called on connect failure
     */
    protected abstract function onCantConnectToHost() : void;

    /**
     * Called on query error
     *
     * @param string $query The query executed when the error occurred
     */
    protected abstract function onQueryError (string $query) : void;

}
