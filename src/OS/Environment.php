<?php

namespace Keruald\OmniTools\OS;

use InvalidArgumentException;

class Environment {

    public static function has (string $key) : bool {
        return array_key_exists($key, $_ENV)
               || array_key_exists($key, $_SERVER);
    }

    /**
     * @throws InvalidArgumentException
     */
    public static function get (string $key) : string {
        if (!self::has($key)) {
            throw new InvalidArgumentException("Key not found: $key");
        }

        return $_ENV[$key] ?? $_SERVER[$key];
    }

    public static function getOr (string $key, string $default) : string {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

}
