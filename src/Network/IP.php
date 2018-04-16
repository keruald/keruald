<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

class IP {

    public static function isIP (string $ip) : bool {
        return self::isIPv4($ip) || self::isIPv6($ip);
    }

    public static function isIPv4 (string $ip) : bool {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) !== false;
    }

    public static function isIPv6 (string $ip) : bool {
        return filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== false;
    }

}
