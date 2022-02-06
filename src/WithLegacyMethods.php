<?php

namespace Keruald\Database;

use BadMethodCallException;

trait WithLegacyMethods {

    private static function getNewMethodName (string $legacyName) : string {
        return match ($legacyName) {
            'sql_nextid' => 'nextId',
            'sql_query_express' => 'queryScalar',
            'sql_fetchrow' => 'fetchRow',
            'sql_numrows' => 'numRows',
            default => substr($legacyName, 4),
        };
    }

    protected function callByLegacyMethodName (string $name, array $arguments) {
        $newMethodName = self::getNewMethodName($name);

        if (!method_exists($this, $newMethodName)) {
            $className = get_class($this);
            throw new BadMethodCallException(
                "Legacy method doesn't exist: $className::$name"
            );
        }

        trigger_error(<<<EOF
\$db->$name calls shall be replaced by \$db->$newMethodName calls.
EOF
            , E_USER_DEPRECATED);

        return call_user_func_array(
            [$this, $newMethodName], $arguments
        );
    }

}
