<?php

namespace Keruald\OmniTools\OS;

use InvalidArgumentException;

use Keruald\OmniTools\DataTypes\Option\Option;

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

    /**
     * Try to get an environment value
     *
     * @param string $key The key of the environment value to get
     *
     * @return Option Some<string> if found, None if not found
     */
    public static function tryGet (string $key) : Option {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;

        return Option::from($value);
    }

    public static function getOr (string $key, string $default) : string {
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

}
