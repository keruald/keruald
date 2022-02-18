<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

use Countable;
use InvalidArgumentException;

abstract class IPRange implements Countable {

    ///
    /// Constructors
    ///

    public static function from (string $format) : self {
        $data = explode("/", $format, 2);

        if (IP::isIPv4($data[0])) {
            return new IPv4Range($data[0], (int)$data[1]);
        }

        if (IP::isIPv6($data[0])) {
            return new IPv6Range($data[0], (int)$data[1]);
        }

        throw new InvalidArgumentException();
    }

    ///
    /// Getters and setters
    ///

    public abstract function getBase () : string;
    public abstract function setBase (string $base) : void;

    public abstract function getNetworkBits () : int;
    public abstract function setNetworkBits (int $networkBits) : void;

    ///
    /// Helper methods
    ///

    public abstract function contains (string $ip) : bool;
    public abstract function getFirst () : string;
    public abstract function getLast () : string;

    ///
    /// Countable methods
    ///

    public abstract function count () : int;

    ///
    /// Data sources
    ///

    /**
     * @return IPRange[]
     */
    public static function getLoopbackRanges () : array {
        return [
            "IPv4" => self::from("127.0.0.0/8"),
            "IPv6" => self::from("::1/128"),
        ];
    }

}
