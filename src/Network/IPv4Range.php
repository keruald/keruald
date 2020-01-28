<?php

namespace Keruald\OmniTools\Network;

use Countable;
use InvalidArgumentException;

class IPv4Range extends IPRange {

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
        if (!IP::isIPv4($base)) {
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
        if ($networkBits < 0 || $networkBits > 32) {
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
        return long2ip(ip2long($this->base) + 2 ** $this->count() - 1);
    }

    public function contains (string $ip) : bool {
        if (!IP::isIP($ip)) {
            throw new InvalidArgumentException;
        }

        if (!IP::isIPv4($ip)) {
            return false;
        }

        $ipAsLong = ip2long($ip);
        $baseAsLong = ip2long($this->base);

        return $ipAsLong >= $baseAsLong
            && $ipAsLong <= $baseAsLong + $this->count() - 1;
    }

    ///
    /// Countable interface
    ///

    public function count () : int {
        return 32 - $this->networkBits;
    }

}
