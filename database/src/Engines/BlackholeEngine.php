<?php

namespace Keruald\Database\Engines;

use Keruald\Database\DatabaseEngine;
use Keruald\Database\Result\DatabaseResult;

class BlackholeEngine extends DatabaseEngine {

    public function escape (string $expression) : string {
        return $expression;
    }

    public function query (string $query) : bool|DatabaseResult {
        return true;
    }

    public function nextId () : int|string {
        return 0;
    }

    public function countAffectedRows () : int {
        return 0;
    }

    protected function getExceptionContext () : array {
        return [];
    }

    public static function load (array $config): DatabaseEngine {
        return new self;
    }

    public function getUnderlyingDriver () : mixed {
        return null;
    }

    public function isExistingTable (string $database, string $table) : bool {
        // Everything and nothing exists in a blackhole
        return true;
    }

}
