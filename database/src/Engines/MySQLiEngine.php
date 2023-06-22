<?php

namespace Keruald\Database\Engines;

use Keruald\Database\DatabaseEngine;
use Keruald\Database\Exceptions\EngineSetupException;
use Keruald\Database\Exceptions\SqlException;

use Keruald\Database\Result\MySQLiDatabaseResult;
use RuntimeException;

use mysqli;
use mysqli_driver;
use mysqli_result;
use mysqli_sql_exception;

class MySQLiEngine extends DatabaseEngine {

    use WithMySQL;

    /**
     * The connection identifier
     */
    private mysqli $db;

    /**
     * The MySQL driver
     */
    private mysqli_driver $driver;

    private int $fetchMode = MYSQLI_ASSOC;

    /**
     * Initializes a new instance of the database abstraction class,
     * for MySQLi engine.
     *
     * @param string $host The host of the MySQL server [optional, default: localhost]
     * @param string $username The username used to connect [optional, default: root]
     * @param string $password The password used to connect [optional, default: empty]
     * @param string $database The database to select [optional]
     */
    function __construct(
        string $host = 'localhost',
        string $username = 'root',
        string $password = '',
        string $database = ''
    ) {
        // Checks extension requirement
        if (!class_exists("mysqli")) {
            throw new RuntimeException("You've chosen to use a MySQLi database engine, but the MySQLi extension is missing.");
        }

        // Connects to MySQL server
        $this->driver = new mysqli_driver();
        $this->db = new mysqli($host, $username, $password);
        $this->setCharset('utf8mb4');

        // Selects database
        if ($database !== '') {
            $this->db->select_db($database);
        }
    }

    /**
     * Sends a unique query to the database.
     *
     * @return mysqli_result|bool For successful SELECT, SHOW, DESCRIBE or
     * EXPLAIN queries, a <b>mysqli_result</b> object; otherwise, true, or false
     * on failure in legacy mode.
     * @throws SqlException if legacy mode is disabled, and the query fails.
     */
    function query (string $query) : MySQLiDatabaseResult|bool {
        // Run query
        try {
            $result = $this->db->query($query);
        } catch (mysqli_sql_exception $ex) {
            if ($this->dontThrowExceptions) {
                return false;
            }

            $this->throwException($ex, $query);
        }

        if (is_bool($result)) {
            return $result;
        }

        return new MySQLiDatabaseResult($result, $this->fetchMode);
    }

    /**
     * Gets more information about the last SQL error.
     *
     * @return array an array with two keys, code and message, containing error information
     * @deprecated The PHP drivers and our abstraction now throw exceptions when an error occur.
     */
    function error () : array {
        return [
            'code' => $this->db->errno,
            'message' => $this->db->error,
        ];
    }

    /**
     * Gets the primary key value of the last query
     * (works only in INSERT context)
     *
     * @return int|string the primary key value
     */
    public function nextId () : int|string {
        return $this->db->insert_id;
    }

    /**
     * Escapes a SQL expression.
     *
     * @param string $expression The expression to escape
     * @return string The escaped expression
     */
    public function escape (string $expression) : string {
        return $this->db->real_escape_string($expression);
    }

    public function countAffectedRows () : int {
        return $this->db->affected_rows;
    }

    ///
    /// MySQL specific
    ///

    /**
     * Sets charset
     */
    public function setCharset ($encoding) {
       $this->db->set_charset($encoding);
    }

    public function setFetchMode (int $mode) {
        $this->fetchMode = $mode;
    }

    ///
    /// Engine mechanics methods
    ///

    private function throwException (mysqli_sql_exception $ex, string $query) {
        $context = $this->getExceptionContext();
        throw SqlException::fromException($ex, $query, $context);
    }

    protected function getExceptionContext () : array {
        return [
            'error' => $this->db->error,
            'errno' => $this->db->errno,
            'errors' => $this->db->error_list,
        ];
    }

    private static function getConfig (array $config) : array {
        return $config + [
            'host' => 'localhost',
            'username' => '',
            'password' => '',
            'database' => '',
            'fetch_mode' => MYSQLI_ASSOC,
        ];
    }

    /**
     * Loads a database instance, connected and ready to process queries.
     *
     * @throws EngineSetupException
     */
    static function load (array $config): MySQLiEngine {
        $config = self::getConfig($config);

        // We need to return an exception if it fails.
        // Switch report mode to the exception throwing one.
        $driver = new mysqli_driver();
        $configuredReportMode = $driver->report_mode;
        $driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

        try {
            $instance = new self(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
            );
        } catch (mysqli_sql_exception $ex) {
            throw new EngineSetupException(
                $ex->getMessage(),
                $ex->getCode(),
                $ex
            );
        }


        // Restore report mode as previously configured
        $driver->report_mode = $configuredReportMode;

        // Extra configuration
        $instance->setFetchMode($config["fetch_mode"]);

        return $instance;
    }

    /**
     * @return mysqli Represents a connection between PHP and a MySQL database.
     */
    public function getUnderlyingDriver (): mysqli {
        return $this->db;
    }

}
