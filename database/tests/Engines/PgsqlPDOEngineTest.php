<?php

namespace Keruald\Database\Tests\Engines;

use Keruald\Database\Engines\PDOEngine;
use Keruald\Database\Engines\PgsqlPDOEngine;
use Keruald\Database\Exceptions\EngineSetupException;

use PDO;
use Pdo\Pgsql;
use PHPUnit\Framework\Attributes\DataProvider;

class PgsqlPDOEngineTest extends BasePDOPostgreSQLTestCase {

    protected function setUp () : void {
        // Skip if not on PHP 8.4+
        if (version_compare(PHP_VERSION, '8.4.0', '<')) {
            $this->markTestSkipped('Pdo\Pgsql requires PHP version >= 8.4.0');
        }

        parent::setUp();
    }

    protected static function buildEngine () : PDOEngine {
        return new PgsqlPDOEngine(
            'localhost',
            'keruald',
            'keruald',
            self::DB_NAME,
        );
    }

    public function testLoad () : void {
        $instance = PgsqlPDOEngine::load([
            'host' => 'localhost',
            'username' => 'keruald',
            'password' => 'keruald',
            'database' => self::DB_NAME,
        ]);

        $this->assertInstanceOf(PDO::class, $instance->getUnderlyingDriver());
    }

    public function testLoadWithWrongPassword(): void {
        $this->expectException(EngineSetupException::class);

        PgsqlPDOEngine::load([
            'host' => 'localhost',
            'username' => 'notexisting',
            'password' => 'notexistingeither',
            'database' => self::DB_NAME,
        ]);
    }

    public function testLoadWithFetchMode(): void {
        $instance = PgsqlPDOEngine::load([
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

    ///
    /// Engine-specific methods
    ///

    public static function provideIdentifiers () : iterable {
        yield "Regular name" => ["foo", '"foo"'];
        yield "No special escape for apostrophe" => ["y'sul", '"y\'sul"'];
        yield "Reserved keyword" => ["user", '"user"'];
    }

    #[DataProvider("provideIdentifiers")]
    public function testEscapeIdentifier ($toEscape, $expected) : void {
        /** @var PgsqlPDOEngine $db */
        $db = $this->db;

        $actual = $db->escapeIdentifier($toEscape);
        $this->assertEquals($expected, $actual);
    }
}
