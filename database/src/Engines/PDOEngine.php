<?php

namespace Keruald\Database\Engines;

use Keruald\Database\DatabaseEngine;

use Keruald\Database\Exceptions\EngineSetupException;
use Keruald\Database\Exceptions\NotImplementedException;
use Keruald\Database\Exceptions\SqlException;
use Keruald\Database\Result\PDODatabaseResult;

use PDO;
use PDOException;
use RuntimeException;

abstract class PDOEngine extends DatabaseEngine {

    protected PDO $db;

    private int $fetchMode = PDO::FETCH_ASSOC;

    protected ?PDOException $lastException = null;

    /**
     * Sends a unique query to the database.
     *
     * @param string $query
     *
     * @return PDODatabaseResult|bool
     */
    public function query (string $query) : PDODatabaseResult|bool {
        try {
            $result = $this->db->query($query);
        } catch (PDOException $ex) {
            if ($this->dontThrowExceptions) {
                return false;
            }

            $this->lastException = $ex;
            $this->onQueryError($query);
        }

        return new PDODatabaseResult($result, $this->fetchMode);
    }

    public function nextId () : int|string {
        return $this->db->lastInsertId();
    }

    protected function getExceptionContext () : array {
        $info = match ($this->lastException) {
            null => $this->db->errorInfo(),
            default => $this->lastException->errorInfo,
        };

        return self::parseErrorInfo($info);
    }

    private static function parseErrorInfo(array $info) : array {
        $context = [];

        // SQLSTATE error code
        if ((int)$info[0] > 0) {
            $context["errno"] = $info[0];
        }

        // Driver-specific error message
        if ($info[2] !== null) {
            $context["error"] = $info[2];
        }

        return $context;
    }

    public static function load (array $config) : DatabaseEngine {
        $config = self::getConfig($config);

        try {
            $instance = new static(
                $config['host'],
                $config['username'],
                $config['password'],
                $config['database'],
            );
        } catch (RuntimeException $ex) {
            throw new EngineSetupException(
                $ex->getMessage(),
                $ex->getCode(),
                $ex
            );
        }

        // Extra configuration
        $instance->fetchMode = (int)$config["fetch_mode"];

        return $instance;
    }

    private static function getConfig (array $config) : array {
        return $config + [
                'host' => 'localhost',
                'username' => '',
                'password' => '',
                'database' => '',
                'fetch_mode' => PDO::FETCH_ASSOC,
            ];
    }

    public function getUnderlyingDriver () : PDO {
        return $this->db;
    }

    public function error () : array {
        return self::parseErrorInfo($this->db->errorInfo());
    }

    ///
    /// Events
    ///

    protected function onCantConnectToHost () : void {
        $previous = $this->lastException;

        $code = match($previous) {
            null => 0,
            default => $previous->getCode(),
        };

        $appendToMessage = match($previous) {
            null => "",
            default => ": " . $previous->getMessage(),
        };

        $ex = new RuntimeException(
            "Can't connect to SQL server" . $appendToMessage,
            $code,
            $previous,
        );

        if (!class_exists(self::EVENTS_PROPAGATION_CLASS)) {
            throw $ex;
        }

        $callable = [self::EVENTS_PROPAGATION_CLASS, "callOrThrow"];
        $callable($this->cantConnectToHostEvents, [$this, $ex], $ex);
    }

    protected function onQueryError (string $query) : void {
        $ex = SqlException::fromQuery(
            $query,
            $this->getExceptionContext(),
        );

        if (!class_exists(self::EVENTS_PROPAGATION_CLASS)) {
            throw $ex;
        }

        $callable = [self::EVENTS_PROPAGATION_CLASS, "callOrThrow"];
        $callable($this->queryErrorEvents, [$this, $query, $ex], $ex);
    }

    ///
    /// Not implemented features
    ///

    /**
     * @throws NotImplementedException
     */
    public function countAffectedRows () : int {
        throw new NotImplementedException(<<<END
            With PDO drivers, you can get the number of affected rows
            for any SQL query using PDODatabaseResult::numRows().
        END);
    }

}
