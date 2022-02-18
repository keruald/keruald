<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Collections;

/**
 * A shared bag is a collection of key and values, which implements
 * a monostate pattern, i.e. there is only one bag, which can be accessed
 * though an arbitrary amount of SharedBag instances.
 *
 * The SharedBag class can be used as:
 *   — shared context, to contain the application configuration
 *   — service locator, to contain application dependencies
 *   — a migration path to store global variables of a legacy application
 *     pending the migration to a collection sharing the same interface
 *
 * Such patterns can be discouraged and as such used with architectural care,
 * as they mainly use SharedBag as global variables, or as an antipattern.
 */
class SharedBag {

    private static ?HashMap $bag = null;

    public function getBag() : HashMap {
        if (self::$bag === null) {
            self::$bag = new HashMap;
        }

        return self::$bag;
    }

}
