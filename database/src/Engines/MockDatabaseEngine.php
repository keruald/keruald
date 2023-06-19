<?php

namespace Keruald\Database\Engines;

use Keruald\Database\DatabaseEngine;
use Keruald\Database\Result\DatabaseResult;
use Keruald\Database\Result\MockDatabaseResult;

use RuntimeException;

class MockDatabaseEngine extends BlackholeEngine {

    private array $query_results = [];

    public function withQueries(array $query_results) : self {
        $this->query_results = $query_results;

        return $this;
    }

    public function query (string $query) : DatabaseResult|bool {
        if (!array_key_exists($query, $this->query_results)) {
            throw new RuntimeException("Unexpected query: " . $query);
        }

        return new MockDatabaseResult($this->query_results[$query]);
    }

    public static function load (array $config) : DatabaseEngine {
        return new self;
    }
}
