<?php

namespace Keruald\OmniTools\HTTP\Requests;

use Keruald\OmniTools\HTTP\URL;
use Keruald\OmniTools\Strings\Multibyte\StringUtilities;

trait WithURL {

    ///
    /// Main methods
    ///

    public static function getServerURL () : string {
        $scheme = self::getScheme();
        $name = self::getServerName();
        $port = self::getPort();

        // If we forward for a proxy, trust the scheme instead of standard :80
        $fixToHTTPS = $port === 80 && $scheme === "https";

        if ($port === 443 || $fixToHTTPS) {
            return "https://$name";
        }

        if ($port === 80) {
            return "http://$name";
        }

        return "$scheme://$name:$port";
    }

    ///
    /// Helper methods
    ///

    public static function getPort () : int {
        return (int)($_SERVER['SERVER_PORT'] ?? 80);
    }

    public static function getServerName () : string {
        return $_SERVER['SERVER_NAME'] ?? "localhost";
    }

    public static function getScheme () : string {
        return $_SERVER['REQUEST_SCHEME']
            ?? $_SERVER['HTTP_X_FORWARDED_PROTO']
            ?? $_SERVER['HTTP_X_FORWARDED_PROTOCOL']
            ?? $_SERVER['HTTP_X_URL_SCHEME']
            ?? self::guessScheme();
    }

    private static function guessScheme () : string {
        return self::isHTTPS() ? "https" : "http";
    }

    public static function isHTTPS () : bool {
        // Legacy headers have been documented at MDN:
        // https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/X-Forwarded-Proto

        $headers = self::getHTTPSHeadersTable();
        foreach ($headers as $header => $value) {
            if (isset($_SERVER[$header]) && $_SERVER[$header] === $value) {
                return true;
            }
        }

        if (isset($_SERVER['HTTP_FORWARDED'])) {
            return StringUtilities::contains($_SERVER['HTTP_FORWARDED'], "proto=https");
        }

        return false;
    }

    private static function getHTTPSHeadersTable () : array {
        return [
            "HTTPS" => "on",
            "REQUEST_SCHEME" => "https",
            "SERVER_PORT" => "443",
            "HTTP_X_FORWARDED_PROTO" => "https",
            "HTTP_FRONT_END_HTTPS" => "on",
            "HTTP_X_FORWARDED_PROTOCOL" => "https",
            "HTTP_X_FORWARDED_SSL" => "on",
            "HTTP_X_URL_SCHEME" => "https",
        ];
    }

    /**
     * Create a URL object, using the current request server URL for protocol
     * and domain name.
     *
     * @param string $query      The query part of the URL [facultative]
     * @param int    $encodeMode Encoding to use for the query [facultative]
     */
    public static function createLocalURL (string $query = "",
                                           int $encodeMode = URL::ENCODE_RFC3986_SLASH_EXCEPTED
    ) : URL {
        return (new URL(self::getServerURL()))
            ->setQuery($query, $encodeMode);
    }

}
