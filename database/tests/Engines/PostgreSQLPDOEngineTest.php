<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\PDOEngine;
use Keruald\Database\Engines\PostgreSQLPDOEngine;
use Keruald\Database\Exceptions\EngineSetupException;

use PDO;

class PostgreSQLPDOEngineTest extends BasePDOPostgreSQLTestCase {

    protected static function buildEngine () : PDOEngine {
        return new PostgreSQLPDOEngine(
            'localhost',
            'keruald',
            'keruald',
            self::DB_NAME,
        );
    }

    public function testLoad () : void {
        $instance = PostgreSQLPDOEngine::load([
            'host' => 'localhost',
            'username' => 'keruald',
            'password' => 'keruald',
            'database' => self::DB_NAME,
        ]);

        $this->assertInstanceOf(PDO::class, $instance->getUnderlyingDriver());
    }

    public function testLoadWithWrongPassword(): void {
        $this->expectException(EngineSetupException::class);

        PostgreSQLPDOEngine::load([
            'host' => 'localhost',
            'username' => 'notexisting',
            'password' => 'notexistingeither',
            'database' => self::DB_NAME,
        ]);
    }

    public function testLoadWithFetchMode(): void {
        $instance = PostgreSQLPDOEngine::load([
            'host' => 'localhost',
            'username' => 'keruald',
            'password' => 'keruald',
            'database' => self::DB_NAME,
            'fetch_mode' => PDO::FETCH_BOTH,
        ]);

        $result = $instance->query("SELECT 1 as num");
        $row = $result->fetchRow();

        // Should have both numeric and associative keys
        $this->assertArrayHasKey(0, $row);
        $this->assertArrayHasKey('num', $row);
    }
}
