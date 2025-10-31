<?php

namespace Keruald\Database\Query;

use Keruald\Database\Engines\PDOEngine;
use Keruald\Database\Exceptions\NotImplementedException;
use Keruald\Database\Result\PDODatabaseResult;

use PDO;
use PDOException;
use PDOStatement;

class PDOQuery extends DatabaseQuery {

    ///
    /// Private members
    ///

    private PDOEngine $db;

    private PDOStatement $statement;

    private int $fetchMode = PDO::FETCH_ASSOC;

    private bool $isInOutDefined = false;

    ///
    /// Constructors
    ///

    public function __construct (PDOEngine $db, PDOStatement $statement) {
        $this->db = $db;
        $this->statement = $statement;
    }

    public static function from (PDOEngine $db, PDOStatement $statement) : self {
        return new self($db, $statement);
    }

    ///
    /// Getters and setters
    ///

    public function getFetchMode () : int {
        return $this->fetchMode;
    }

    public function setFetchMode (int $mode) : void {
        $this->fetchMode = $mode;
    }

    public function withFetchMode (int $mode) : self {
        $this->fetchMode = $mode;

        return $this;
    }

    ///
    /// PDO statements like interaction
    ///

    public function query() : ?PDODatabaseResult {
        if ($this->isInOutDefined && !$this->db->hasInOutSupport()) {
            throw new NotImplementedException("InOut parameters are not supported by this engine.");
        }

        try {
            $result = $this->statement->execute();
        } catch (PDOException $ex) {
            if ($this->db->dontThrowExceptions) {
                return null;
            }

            $this->db->setLastException($ex);
            $this->db->onQueryError($this->statement->queryString);
        }

        return new PDODatabaseResult($this->statement, $this->fetchMode);
    }

    public function with (int|string $name, mixed $value, ?int $type = null) : self {
        $type = $type ?? self::resolveParameterType($value);
        $this->statement->bindValue($name, $value, $type);

        return $this;
    }

    public function withIndexedValue(int $position, mixed $value, ?int $type = null) : self {
        $type = $type ?? self::resolveParameterType($value);
        $this->statement->bindValue($position, $value, $type);

        return $this;
    }

    public function withValue(string $name, mixed $value, ?int $type = null) : self {
        $type = $type ?? self::resolveParameterType($value);
        $this->statement->bindValue($name, $value, $type);

        return $this;
    }

    public function bind(string $name, mixed &$value, ?int $type = null) : self {
        $type = $type ?? self::resolveParameterType($value);
        $this->statement->bindParam($name, $value, $type);

        return $this;
    }

    public function bindInOutParameter(string $name, mixed &$value, ?int $type = null) : self {
        $type = $type ?? self::resolveParameterType($value);
        $this->statement->bindParam($name, $value, $type | PDO::PARAM_INPUT_OUTPUT);

        $this->isInOutDefined = true;

        return $this;
    }

    ///
    /// PDO_PARAM_* type resolution
    ///

    public static function resolveParameterType(mixed $value) : int {
        if (is_int($value)) {
            return PDO::PARAM_INT;
        }

        if (is_null($value)) {
            return PDO::PARAM_NULL;
        }

        if (is_bool($value)) {
            return PDO::PARAM_BOOL;
        }

        return PDO::PARAM_STR;
    }

    ///
    /// Low-level interactions
    ///

    public function getUnderlyingStatement () : PDOStatement {
        return $this->statement;
    }

    ///
    /// Implements Stringable
    ///

    public function __toString () : string {
        return $this->statement->queryString;
    }

}
