<?php

namespace Keruald\Database\Engines;

use Keruald\Database\Exceptions\NotImplementedException;
use PDO;
use Pdo\Pgsql;
use RuntimeException;

final class PgsqlPDOEngine extends PDOEngine {

    use WithPDOPostgreSQL;

    const string PDO_CLASS = Pgsql::class;

    ///
    /// Constructor
    ///

    public function __construct (
        string $host = 'localhost',
        string $username = 'root',
        string $password = '',
        string $database = ''
    ) {
        // Checks extension requirement
        if (!class_exists(self::PDO_CLASS)) {
            throw new RuntimeException("This engine requires PHP 8.4+ Pdo\Pgsql PostgreSQL PDO driver.");
        }

        // Connects to PostgreSQL server
        $dsn = "pgsql:host=$host";
        if ($database !== "") {
            $dsn .= ";dbname=$database";
        }

        $this->db = new Pgsql($dsn, $username, $password);
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

    ///
    /// Engine-specific methods
    ///

    public function escapeIdentifier (string $expression) : string {
        return $this->db->escapeIdentifier($expression);
    }

}
