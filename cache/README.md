# keruald/cache

This library offers a simple layer of abstraction
for cache operations, with concrete implementations.

This cache implementation is compatible with PSR-16.

This cache implementation is NOT compatible with PSR-6.

## Configuration

To get a cache instance, you need to pass configuration as an array.

The properties and values depend on the engine you want to use.

### Memcached

| Key           | Value                          | Default     |
|---------------|--------------------------------|:------------|
| engine        | MemcachedCache class reference |             |
| server        | The memcached hostname         | "localhost" |
| port          | The memcached port             | 11211       |
| sasl_username | The SASL username              |             |
| sasl_password | The SASL password              | ""          |

### Redis

| Key      | Value                          | Default     |
|----------|--------------------------------|:------------|
| engine   | MemcachedCache class reference |             |
| server   | The memcached hostname         | "localhost" |
| port     | The memcached port             | 6379        |
| database | The redis database number      | 0           |

### Void

This cache allows unit tests or to offer a default cache,
when no other configuration is offered.

| Key        | Value                     |
|------------|---------------------------|
| engine     | VoidCache class reference |
