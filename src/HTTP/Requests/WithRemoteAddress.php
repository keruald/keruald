<?php
declare(strict_types=1);

namespace Keruald\OmniTools\HTTP\Requests;

trait WithRemoteAddress {

    /**
     * Gets remote IP address.
     *
     * This is intended as a drop-in replacement for $_SERVER['REMOTE_ADDR'],
     * which takes in consideration proxy values, blindly trusted.
     *
     * This method should is only for environment where headers are controlled,
     * like nginx + php_fpm, where HTTP_ headers are reserved for the server
     * information, and where the headers sent by the web server to nginx are
     * checked or populated by nginx itself.
     *
     * @return string the remote address
     */
    public static function getRemoteAddress () : string {
        return RemoteAddress::fromServer()->getClientAddress();
    }

}
