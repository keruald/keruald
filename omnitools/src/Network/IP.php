<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

use InvalidArgumentException;

abstract class IP {

    ///
    /// Helper methods
    ///

    public static function isIP (string $ip) : bool {
        return self::isIPv4($ip) || self::isIPv6($ip);
    }

    public static function isIPv4 (string $ip) : bool {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public static function isIPv6 (string $ip) : bool {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

    public static function isLoopback (string $ip) : bool {
        $ranges = IPRange::getLoopbackRanges();
        foreach ($ranges as $range) {
            if ($range->contains($ip)) {
                return true;
            }
        }

        return false;
    }

    ///
    /// Constructor
    ///

    public static function from (string $ip) : IP {
        if (self::isIPv4($ip)) {
            return new IPv4($ip);
        }

        if (self::isIPv6($ip)) {
            $ipv6 = new IPv6($ip);
            $ipv6->normalize();
            return $ipv6;
        }

        throw new InvalidArgumentException;
    }

    public abstract function __toString () : string;

    public abstract function getDomain () : int;

}
