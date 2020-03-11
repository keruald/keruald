<?php

namespace Keruald\OmniTools\Network;

use InvalidArgumentException;

class IPv6Range extends IPRange {

    /**
     * @var string
     */
    private $base;

    /**
     * @var int
     */
    private $networkBits;

    ///
    /// Constructors
    ///

    public function __construct (string $base, int $networkBits) {
        $this->setBase($base);
        $this->setNetworkBits($networkBits);
    }

    ///
    /// Getters and setters
    ///

    /**
     * @return string
     */
    public function getBase () : string {
        return $this->base;
    }

    /**
     * @param string $base
     */
    public function setBase (string $base) : void {
        if (!IP::isIPv6($base)) {
            throw new InvalidArgumentException;
        }

        $this->base = $base;
    }

    /**
     * @return int
     */
    public function getNetworkBits () : int {
        return $this->networkBits;
    }

    /**
     * @param int $networkBits
     */
    public function setNetworkBits (int $networkBits) : void {
        if ($networkBits < 0 || $networkBits > 128) {
            throw new InvalidArgumentException;
        }

        $this->networkBits = $networkBits;
    }

    ///
    /// Helper methods
    ///

    public function getFirst () : string {
        return $this->base;
    }

    public function getLast () : string {
        if ($this->count() === 0) {
            return $this->base;
        }

        $base = inet_pton($this->getFirst());
        $mask = inet_pton($this->getInverseMask());
        return inet_ntop($base | $mask);
    }

    private function getInverseMask () : string {
        $bits = array_fill(0, $this->networkBits, 0) + array_fill(0, 128, 1);

        return (string)IPv6::fromBinaryBits($bits);
    }

    public function contains (string $ip) : bool {
        if (!IP::isIP($ip)) {
            throw new InvalidArgumentException;
        }

        if (IP::isIPv4($ip)) {
            $ip = "::ffff:" . $ip; // IPv4-mapped IPv6 address
        }

        $baseAsNumericBinary = inet_pton($this->getFirst());
        $lastAsNumericBinary = inet_pton($this->getLast());
        $ipAsNumericBinary   = inet_pton($ip);

        return strlen($ipAsNumericBinary) == strlen($baseAsNumericBinary)
            && $ipAsNumericBinary >= $baseAsNumericBinary
            && $ipAsNumericBinary <= $lastAsNumericBinary;
    }

    ///
    /// Countable interface
    ///

    public function count () : int {
        return 128 - $this->networkBits;
    }

}
