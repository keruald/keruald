<?php

namespace Keruald\OmniTools\Network;

use InvalidArgumentException;

class IPv6 extends IP {

    /**
     * @var string
     */
    private $ip;

    ///
    /// Constructors
    ///

    public function __construct (string $ip) {
        $this->ip = $ip;
    }

    public static function from (string $ip) : self {
        $ipv6 = new self($ip);

        if (!$ipv6->isValid()) {
            throw new InvalidArgumentException;
        }

        $ipv6->normalize();

        return $ipv6;
    }

    public static function fromBinaryBits (array $bits) : self {
        $fullBits = $bits + array_fill(0, 128, 0);
        $hextets = [];

        for ($i = 0 ; $i < 8 ; $i++) {
            // Read 16 bits
            $slice = implode("", array_slice($fullBits, $i * 16, 16));
            $hextets[] = base_convert($slice, 2, 16);
        }

        return self::from(implode(":", $hextets));
    }

    ///
    /// Helper methods
    ///

    public function isValid () : bool {
        return IP::isIPv6($this->ip);
    }

    public function increment (int $increment = 1) : self {
        if ($increment === 0) {
            return $this;
        }

        if ($increment < 0) {
            throw new InvalidArgumentException("This method doesn't support decrementation.");
        }

        $ipAsNumericBinary = inet_pton($this->ip);

        // See https://gist.github.com/little-apps/88bbd23576008a84e0b6
        $i = strlen($ipAsNumericBinary) - 1;
        $remainder = $increment;

        while ($remainder > 0 && $i >= 0) {
            $sum = ord($ipAsNumericBinary[$i]) + $remainder;
            $remainder = $sum / 256;
            $ipAsNumericBinary[$i] = chr($sum % 256);

            --$i;
        }

        $this->ip = inet_ntop($ipAsNumericBinary);
        return $this;
    }

    public function normalize () : self {
        $this->ip = inet_ntop(inet_pton($this->ip));
        return $this;
    }

    public function isNormalized() : bool {
        return $this->ip === inet_ntop(inet_pton($this->ip));
    }

    ///
    /// Magic methods
    ///

    public function __toString () : string {
        return $this->ip;
    }
}
