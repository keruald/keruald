<?php

namespace Keruald\Database\Engines;

trait WithMySQL {

    public function isExistingTable (string $database, string $table) : bool {
        $escapedTable = $this->escape($table);
        $sql = "SHOW TABLE STATUS
                FROM `$database`
                WHERE Name = '$escapedTable'";

        return $this->queryScalar($sql) === $table;
    }

    public function isView (string $database, string $view) : bool {
        $escapedView = $this->escape($view);
        $escapedDatabase = $this->escape($database);

        $sql = <<<EOF
SELECT TABLE_TYPE
FROM information_schema.tables
WHERE TABLE_SCHEMA = "$escapedDatabase" AND TABLE_NAME = "$escapedView"
EOF;

        return $this->queryScalar($sql) === "VIEW";
    }

}
