<?php

namespace Keruald\Database\Tests\Exceptions;

use Keruald\Database\Exceptions\SqlException;

use PHPUnit\Framework\TestCase;

class SqlExceptionTest extends TestCase {

    public function testGetQuery () {
        $state = [
            "error" => 'Syntax error',
            "errno" => 1064,
        ];

        $sql = 'SELECT 1+';
        $ex = SqlException::fromQuery($sql, $state);
        $this->assertEquals(
            $sql,
            $ex->getQuery(),
            ""
        );

        $ex = SqlException::fromQuery("", $state);
        $this->assertEquals(
            "",
            $ex->getQuery(),
            "If the query isn't specified during the constructor call, getQuery shall not return null but must return an empty string too."
        );
    }

}
