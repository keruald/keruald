<?php
declare(strict_types=1);

namespace Keruald\Cache\Features;

use InvalidArgumentException;

trait WithPrefix {

    ///
    /// Properties
    ///

    private string $prefix = "";

    ///
    /// Getters and setters
    ///

    public function getPrefix () : string {
        if ($this->prefix === "") {
            throw new InvalidArgumentException("This cache doesn't use prefix");
        }

        return $this->prefix;
    }

    protected function getUnsafePrefix () : string {
        return $this->prefix;
    }

    public function hasPrefix () : bool {
        return $this->prefix !== "";
    }

    /**
     * Allows to share ab instance with several applications
     * by prefixing the keys.
     *
     * @throws InvalidArgumentException
     */
    public function setPrefix (string $prefix) : self {
        if ($prefix === "") {
            throw new InvalidArgumentException("Prefix must be a non-empty string");
        }

        $this->prefix = $prefix;

        return $this;
    }

    public function clearPrefix () : self {
        $this->prefix = "";

        return $this;
    }

}
