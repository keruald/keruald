<?php
declare(strict_types=1);

namespace Keruald\Cache\Engines;

use Keruald\Cache\Cache;

use DateInterval;

/**
 * "blackhole" void cache
 *
  * This class doesn't cache information, it's void wrapper
 *  get will always return null
 *  set and delete do nothing
 *
 * It will be used by default if no cache is specified.
 */
class CacheVoid extends Cache {

    static function load ($config) : self {
        return new static;
    }

    function get (string $key, mixed $default = null) : mixed {
       return $default;
    }

    function set (
        string $key,
        mixed $value,
        null|int|DateInterval $ttl = null,
    ) : bool {
        return true;
    }

    function delete (string $key) : bool {
        return true;
    }

    public function clear () : bool {
        return true;
    }

    public function has (string $key) : bool {
        return false;
    }

    public function getMultiple (
        iterable $keys,
        mixed    $default = null
    ) : iterable {
        foreach ($keys as $key) {
            yield $key => $default;
        }
    }

    public function setMultiple (
        iterable $values,
        null|int|DateInterval $ttl = null,
    ) : bool {
        return true;
    }

    public function deleteMultiple (iterable $keys) : bool {
        return true;
    }

}
