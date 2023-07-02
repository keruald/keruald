<?php
declare(strict_types=1);

namespace Keruald\Cache\Engines;

use Keruald\Cache\Cache;
use Keruald\Cache\Exceptions\CacheException;
use Keruald\Cache\Features\WithPrefix;
use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\Collections\Vector;

use Memcached;

/**
 * Memcached cache
 *
 * /!\ This class uses the Memcached extension AND NOT Memcache.
 *
 * References:
 *
 * @link https://www.php.net/manual/en/book.memcached.php
 * @link https://memcached.org
 */
class CacheMemcached extends Cache {

    use WithPrefix;

    ///
    /// Constants - default value
    ///

    const DEFAULT_SERVER = "localhost";

    const DEFAULT_PORT = 11211;

    ///
    /// Properties
    ///

    private Memcached $memcached;

    ///
    /// Constructors
    ///

    public function __construct (Memcached $memcached) {
        $this->memcached = $memcached;
    }

    public static function load (array $config) : self {
        //Checks extension is okay
        if (!extension_loaded('memcached')) {
            if (extension_loaded('memcache')) {
                throw new CacheException("Can't initialize Memcached cache engine: PHP extension memcached not loaded. This class uses the Memcached extension AND NOT the Memcache extension (this one is loaded).</strong>");
            } else {
                throw new CacheException("Can't initialize Memcached cache engine: PHP extension memcached not loaded.");
            }
        }

        $memcached = new Memcached;
        $memcached->addServer(
            $config["server"] ?? self::DEFAULT_SERVER,
            $config["port"] ?? self::DEFAULT_PORT,
        );

        // SASL authentication
        if (array_key_exists("sasl_username", $config)) {
            $memcached->setOption(Memcached::OPT_BINARY_PROTOCOL, true);
            $memcached->setSaslAuthData(
                $config["sasl_username"],
                $config["sasl_password"] ?? "",
            );
        }

        return new self($memcached);
    }

    ///
    /// Cache operations
    ///

    /**
     * Gets the specified key's data
     */
    function get (string $key, mixed $default = null) : mixed {
        $key = $this->getUnsafePrefix() . $key;

        $result = $this->memcached->get($key);

        return match ($result) {
            false => $default,
            default => unserialize($result),
        };
    }

    /**
     * Sets the specified data at the specified key
     */
    function set (
        string $key,
        mixed $value,
        null|int|\DateInterval $ttl = null
    ) : bool {
        $key = $this->getUnsafePrefix() . $key;

        return $this->memcached->set($key, serialize($value));
    }

    /**
     * Deletes the specified key's data
     *
     * @param string $key the key to delete
     */
    function delete (string $key) : bool {
        $key = $this->getUnsafePrefix() . $key;

        return $this->memcached->delete($key);
    }

    public function clear () : bool {
        $keys = $this->memcached->getAllKeys();

        if ($keys === false) {
            return false;
        }

        if ($this->hasPrefix()) {
            // Restrict to our keys, as we don't use Memcached::OPT_PREFIX_KEY
            $prefix = $this->getUnsafePrefix();
            $keys = Vector::from($keys)
                          ->filter(fn($key) => str_starts_with($key, $prefix))
                          ->toArray();
        }

        $result = $this->memcached->deleteMulti($keys);
        return self::areAllOperationsSuccessful($result);
    }

    public function has (string $key) : bool {
        $key = $this->getUnsafePrefix() . $key;

        $this->memcached->get($key);

        return match ($this->memcached->getResultCode()) {
            Memcached::RES_NOTFOUND => false,
            default => true,
        };
    }

    ///
    /// Helper methods
    ///

    private static function areAllOperationsSuccessful (array $result) : bool {
        return HashMap::from($result)
            ->all(function ($key, $value) {
                return $value === true; // can be true or Memcached::RES_*
            });
    }

}
