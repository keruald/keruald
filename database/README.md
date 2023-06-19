# keruald/database

This library offers a simple layer of abstraction for database operations.

## Configuration

To get a database instance, you need to pass configuration as an array.
The properties and values depend on the engine you want to use.

### MySQLi

|    Key   | Value                                |          |
|----------|--------------------------------------|:--------:|
| engine   | MySQLiEngine class reference         |          |
| host     | The MySQL hostname, e.g. "localhost" |          |
| username | The MySQL user to use for connection |          |
| password | The clear text password to use       |          |
| database | The default db to select for queries | optional |

For example:

```php
[
    'engine' => Keruald\Database\Engines\MySQLiEngine::class,
    'host' => 'localhost',
    'username' => 'app',
    'password' => 'someSecret',
    'database' => 'app',          // optional
]
```

## Legacy drivers

The mysql extension has been deprecated in PHP 5.7 and removed in PHP 7.
As such, this extension isn't supported anymore. You can use straightforwardly
replace 'MySQL' by 'MySQLi' as engine.

## Specialized drivers for tests
### Blackhole

The blackhole engine does nothing and always returns `true` as query result.

This engine can be used for mocks:

  - directly, when database behavior does not matter
  - to build a mock by overriding behavior of query() or any other method

It can also be used with the loader, without any engine-specific configuration:

```php
[
    'engine' => Keruald\Database\Engines\BlackholeEngine::class,
]
```

### MockDatabaseEngine

The mock database is a simple implementation of the blackhole engine as mocking
service to use when you want to return a deterministic response to known
queries.

A benefit is you don't need a running database server for your unit tests.

You can pass to the `withQueries` method an array with one item per query:
  - key: the SQL query
  - value: an array with all rows for that query
  -
For example:

```php
    public function testGetFruits () : void {
        $queries = [
            "SELECT name, color FROM fruits" => [
                [ "name" => "strawberry", "color" => "red" ],
                [ "name" => "blueberry", "color" => "violet" ],
            ],
        ];

        $db = (new MockDatabaseEngine())
            ->withQueries($queries);

        // Inject $db to a class and test it
    }
```

To return only one row, you can use `[[…]]` to represent an array of one array:

```php
        $queries = [
            "SELECT 1+1" => [[ "1+1" => 2 ]],
        ];
```

The queries results are then wrapped in the MockDatabaseResult class.

When the query doesn't exist, an exception is thrown.

We recommend a mixed approach of the Blackhole engine when results don't matter,
and of this class when you need some control on it.
