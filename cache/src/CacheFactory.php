<?php
declare(strict_types=1);

namespace Keruald\Cache;

use Keruald\Cache\Engines\CacheVoid;
use Keruald\Cache\Exceptions\CacheException;

/**
 * Cache caller
 */
class CacheFactory {

    const DEFAULT_ENGINE = CacheVoid::class;

    /**
     * Loads the cache instance, building it according a configuration array.
     *
     * The correct cache instance to initialize will be determined from the
     * 'engine' key. It should match the name of a Cache class.
     *
     * This method will create an instance of the specified object,
     * calling the load static method from this object class.
     *
     * Example:
     * <code>
     * $config['engine'] = CacheQuux::class;
     * $cache = Cache::load($config); //Cache:load() will call CacheQuux:load();
     * </code>
     *
     * @return Cache the cache instance
     * @throws CacheException
     */
    static function load (array $config) : Cache {
        $engine = $config["engine"] ?? self::DEFAULT_ENGINE;

        if (!class_exists($engine)) {
            throw new CacheException("Can't initialize $engine cache engine. The class can't be found.");
        }

        return call_user_func([$engine, 'load'], $config);
    }

}
