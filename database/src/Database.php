<?php

namespace Keruald\Database;

use Keruald\Database\Exceptions\EngineSetupException;

/**
 * Represents a database.
 *
 * The Database class allows to load from a configuration the correct driver.
 *
 * It can be instanced in two modes:
 *
 *   1) through Database::initialize() if you directly want a database object
 *      or to store in a service container.
 *
 *   2) through Database::load() if you want to use a singleton pattern.
 */
abstract class Database {

    ///
    /// Factory pattern
    ///

    /**
     * Gets and initializes a database instance
     *
     * The correct database instance to initialize will be determined from the
     * $Config['database']['engine'] preference. Expected value is an instance
     * of DatabaseEngine.
     *
     * Example:
     * <code>
     * $Config['database']['engine'] = 'Foo\Quux';
     * $db = Database::initialize(); // will call Foo\Quux::load();
     * </code>
     */
    static function initialize (array &$config) : DatabaseEngine {
        $engine_class = self::getEngineClass($config);
        $instance = call_user_func([$engine_class, 'load'], $config);

        $instance->dontThrowExceptions =
            $config['dontThrowExceptions'] ?? false;

        unset($config['password']);

        return $instance;
    }

    private static function getEngineClass (array $config) : string {
        if (!array_key_exists('engine', $config)) {
            throw new EngineSetupException(<<<'EOF'
No database engine is configured. Engine key must be defined.
EOF
            );
        }

        $engine_class = $config['engine'];

        if (!class_exists($engine_class)) {
            throw new EngineSetupException(
                "Database engine $engine_class class not found."
            );
        }

        return $engine_class;
    }

    ///
    /// Singleton pattern
    ///

    /**
     * @var DatabaseEngine|null The instance
     */
    private static ?DatabaseEngine $instance = null;

    /**
     * Gets the database instance, initializing it if needed
     *
     * Example:
     * <code>
     * $Config['database']['engine'] = 'Foo\Quux';
     * $db = Database::load($Config['database']); // will call Foo\Quux::load()
     * […]
     * $db = Database::load($Config['database']); // will return previously
     *                                            // initialized engine instance
     * </code>
     *
     * @return DatabaseEngine the database instance
     */
    public static function load (array &$config) : DatabaseEngine {
        if (self::$instance === null) {
            self::$instance = self::initialize($config);
        }

        return self::$instance;
    }

}
