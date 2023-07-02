<?php
declare(strict_types=1);

namespace Keruald\Cache;

use Keruald\OmniTools\Collections\MultipleOperation;
use Psr\SimpleCache\CacheInterface;

use DateInterval;

abstract class Cache implements CacheInterface {

    ///
    /// Loader
    ///

    public abstract static function load (array $config) : Cache;

    ///
    /// Default implementation for CacheInterface -Multiple methods
    ///

    public function getMultiple (
        iterable $keys,
        mixed    $default = null
    ) : iterable {
        foreach ($keys as $key) {
            yield $key => $this->get($key, $default);
        }
    }

    public function setMultiple (
        iterable $values,
        DateInterval|int|null $ttl = null
    ) : bool {
        return MultipleOperation::do(
            $values,
            fn($key, $value) => $this->set($key, $value, $ttl)
        );
    }

    public function deleteMultiple (iterable $keys) : bool {
        return MultipleOperation::do(
            $keys,
            fn($key) => $this->delete($key)
        );
    }

}
