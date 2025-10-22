<?php

namespace Keruald\Database\Engines;

use LogicException;
use PDOException;

trait WithPDOPostgreSQL {

    public function isExistingTable (string $database, string $table) : bool {
        /** @var \PDO $db */
        $db = $this->getUnderlyingDriver();

        $sql = "SELECT EXISTS (
                    SELECT FROM pg_tables
                    WHERE schemaname = ?
                    AND tablename = ?)::int;";

        $stmt = $db->prepare($sql);
        $stmt->execute([$database, $table]);

        $result = $stmt->fetchColumn();
        return (bool)$result;
    }

    public function queryScalar (string $query = "") : string {
        if ($query === "") {
            return "";
        }

        $result = $this->query($query);

        $row = $result->fetchRow();
        if ($row !== null) {
            foreach ($row as $value) {
                return (string)$value;
            }
        }

        // No item
        throw new LogicException("The queryScalar method is intended
            to be used with SELECT queries and assimilated");
    }

}
