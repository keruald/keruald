<?php

namespace Keruald\Cache\Engines;

use Keruald\Cache\Cache;
use Keruald\Cache\Exceptions\CacheException;

use DateInterval;
use DateTimeImmutable;

use Redis;
use RedisException;

class CacheRedis extends Cache {

    ///
    /// Constants - default value
    ///

    const DEFAULT_SERVER = "localhost";

    const DEFAULT_PORT = 6379;

    ///
    /// Properties
    ///

    private Redis $redis;

    ///
    /// Constructors
    ///

    public function __construct (Redis $client) {
        $this->redis = $client;
    }

    public static function load (array $config) : Cache {
        //Checks extension is okay
        if (!extension_loaded("redis")) {
            throw new CacheException("Can't initialize Redis cache engine: PHP extension redis not loaded.");
        }

        $client = new Redis();
        try {
            $client->connect(
                $config["server"] ?? self::DEFAULT_SERVER,
                $config["port"] ?? self::DEFAULT_PORT,
            );

            if (array_key_exists("database", $config)) {
                $client->select($config["database"]);
            }

        } catch (RedisException $ex) {
            throw new CacheException(
                "Can't initialize Redis cache engine: Can't connect to Redis server",
                0,
                $ex
            );
        }

        return new self($client);
    }

    ///
    /// Cache operations
    ///

    public function get (string $key, mixed $default = null) : mixed {
        try {
            $value = $this->redis->get($key);
        } catch (RedisException $ex) {
            throw new CacheException("Can't get item", 0, $ex);
        }

        return match ($value) {
            false => $default,
            default => unserialize($value),
        };
    }

    function set (
        string $key,
        mixed $value,
        null|int|DateInterval $ttl = null
    ) : bool {
        try {
            if ($ttl === null) {
                $this->redis->set($key, serialize($value));
            } else {
                $this->redis->setex($key, self::parse_interval($ttl), $value);
            }
        } catch (RedisException $ex) {
            throw new CacheException("Can't set item", 0, $ex);
        }

        return true;
    }

    public function delete (string $key) : bool {
        try {
            $countDeleted = $this->redis->del($key);
        } catch (RedisException $e) {
            throw new CacheException("Can't delete item", 0, $ex);
        }

        return $countDeleted === 1;
    }

    public function clear () : bool {
        try {
            $this->redis->flushDB();
        } catch (RedisException $e) {
            throw new CacheException("Can't clear cache", 0, $ex);
        }

        return true;
    }

    public function has (string $key) : bool {
        try {
            $count = $this->redis->exists($key);
        } catch (RedisException $e) {
            throw new CacheException("Can't check item", 0, $ex);
        }

        return $count === 1;
    }

    ///
    /// Overrides for multiple operations
    ///

    public function deleteMultiple (iterable $keys) : bool {
        $keys = [...$keys];
        $expectedCount = count($keys);

        try {
            $countDeleted = $this->redis->del($keys);
        } catch (RedisException $e) {
            throw new CacheException("Can't delete items", 0, $ex);
        }

        return $countDeleted === $expectedCount;
    }

    ///
    /// Helper methods
    ///

    private static function parse_interval (DateInterval|int $ttl) : int {
        if (is_integer($ttl)) {
            return $ttl;
        }

        $start = new DateTimeImmutable;
        $end = $start->add($ttl);

        return $end->getTimestamp() - $start->getTimestamp();
    }

}
