<?php

namespace Keruald\Database\Tests\Query;

use Keruald\Database\Engines\PDOEngine;
use Keruald\Database\Query\PDOQuery;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use PDO;
use PDOStatement;

class PDOQueryTest extends TestCase {

    private PDOQuery $query;

    ///
    /// Tests set up
    ///

    protected function setUp() : void {
        $this->query = $this->mockQuery();
    }

    protected function mockQuery () : PDOQuery {
        $engine = $this->createMock(PDOEngine::class);
        $statement = $this->createMock(PDOStatement::class);

        return new PDOQuery($engine, $statement);
    }

    ///
    /// Getters and setters
    ///

    public function testGetAndSetFetchMode () : void {
        $this->query->setFetchMode(PDO::FETCH_ASSOC);
        $this->assertEquals(PDO::FETCH_ASSOC, $this->query->getFetchMode());
    }

    ///
    /// Static methods
    ///

    public static function provideParameterTypes () : iterable {
        yield "int" => [ PDO::PARAM_INT, 1 ];
        yield "falsy int" => [ PDO::PARAM_INT, 0 ];
        yield "negative int" => [ PDO::PARAM_INT, -1 ];

        yield "bool" => [ PDO::PARAM_BOOL, true ];
        yield "falsy bool" => [ PDO::PARAM_BOOL, false ];

        yield "null" => [ PDO::PARAM_NULL, null ];

        yield "string" => [ PDO::PARAM_STR, "foo" ];
        yield "empty string" => [ PDO::PARAM_STR, "" ];
        yield "zero string" => [ PDO::PARAM_STR, "0" ];

        // Anything else should also be treated as a string
        yield "float" => [ PDO::PARAM_STR, 1.0 ];
        yield "zero float" => [ PDO::PARAM_STR, 0.0 ];
    }

    #[DataProvider("provideParameterTypes")]
    public function testResolveParameterType ($type, $value) {
        $this->assertEquals($type, PDOQuery::resolveParameterType($value));
    }

}
