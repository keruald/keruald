<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

use InvalidArgumentException;

class IPv4 extends IP {

    ///
    /// Constants
    ///

    const DOMAIN = 2; // AF_INET

    ///
    /// Properties
    ///

    private string $ip;

    ///
    /// Constructors
    ///

    public function __construct (string $ip) {
        $this->ip = $ip;
    }

    public static function from (string $ip) : self {
        $ipv4 = new self($ip);

        if (!$ipv4->isValid()) {
            throw new InvalidArgumentException("Address is not a valid IPv4.");
        }

        return $ipv4;
    }

    public function isValid () : bool {
        return IP::isIPv4($this->ip);
    }

    public function getDomain () : int {
        return self::DOMAIN; // AF_INET
    }

    ///
    /// Magic methods
    ///

    public function __toString () : string {
        return $this->ip;
    }

}
