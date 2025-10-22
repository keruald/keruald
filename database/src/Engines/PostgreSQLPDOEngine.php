<?php

namespace Keruald\Database\Engines;

use Keruald\Database\Exceptions\NotImplementedException;

use PDO;
use PDOException;
use RuntimeException;

class PostgreSQLPDOEngine extends PDOEngine {

    use WithPDOPostgreSQL;

    const string PDO_CLASS = PDO::class;

    public function __construct (
        string $host = 'localhost',
        string $username = 'root',
        string $password = '',
        string $database = ''
    ) {
        // Checks extension requirement
        if (!class_exists(self::PDO_CLASS)) {
            throw new RuntimeException("This engine requires PDO extension.");
        }

        // Connects to PostgreSQL server
        $dsn = "pgsql:host=$host";
        if ($database !== "") {
            $dsn .= ";dbname=$database";
        }

        try {
            $this->db = new PDO($dsn, $username, $password);
        } catch (PDOException $ex) {
            $this->lastException = $ex;
            $this->onCantConnectToHost();

            return;
        }

        $this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    }

    ///
    /// Not implemented features
    ///

    /**
     * @throws NotImplementedException
     */
    public function escape (string $expression) : string {
        throw new NotImplementedException(<<<END
            This PDO engine does not support escape for literals.
            Placeholders are recommended instead for PDO operations.
        END);
    }

}
