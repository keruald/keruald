<?php
declare(strict_types=1);

namespace Keruald\OmniTools\HTTP;

use Keruald\OmniTools\Strings\Multibyte\OmniString;

class URL {

    ///
    /// Constants
    ///

    /**
     * Encode the query using RFC 3986, but keep / intact as a separators.
     * As such, everything will be encoded excepted ~ - _ . / characters.
     */
    const ENCODE_RFC3986_SLASH_EXCEPTED = 1;

    /**
     * Encode the query using RFC 3986, including the /.
     * As such, everything will be encoded excepted ~ - _ . characters.
     */
    const ENCODE_RFC3986_PURE = 2;

    /**
     * Consider the query already encoded.
     */
    const ENCODE_AS_IS = 3;

    ///
    /// Private members
    ///

    /**
     * @var string
     */
    private $url;

    /**
     * @var int
     */
    private $queryEncoding;

    ///
    /// Constructors
    ///

    public function __construct ($url,
                                 $queryEncoding = self::ENCODE_RFC3986_SLASH_EXCEPTED) {
       $this->url = $url;
       $this->queryEncoding = $queryEncoding;
    }

    public static function compose (string $protocol, string $domain,
                                    string $query,
                                    $queryEncoding = self::ENCODE_RFC3986_SLASH_EXCEPTED
    ) : self {
        return (new URL("", $queryEncoding))
            ->update($protocol, $domain, $query);
    }


    ///
    /// Getters and setters
    ///

    public function getProtocol () : string {
        if (preg_match("@(.*?)://.*@", $this->url, $matches)) {
            return $matches[1];
        }

        return "";
    }

    private function getUrlParts() : array {
        preg_match("@://(.*)@", $this->url, $matches);
        return explode("/", $matches[1], 2);
    }

    public function getDomain () : string {
        if (strpos($this->url, "://") === false) {
            return "";
        }

        $domain = $this->getUrlParts()[0];

        if ($domain === "") {
            return "";
        }

        return self::beautifyDomain($domain);
    }

    public function getQuery () : string {
        if (strpos($this->url, "://") === false) {
            return $this->url;
        }

        $parts = $this->getUrlParts();

        if (count($parts) < 2 || $parts[1] === "" || $parts[1] === "/") {
            return "/";
        }

        return "/" . $this->beautifyQuery($parts[1]);
    }

    public function setProtocol ($protocol) : self {
        $this->update($protocol, $this->getDomain(), $this->getQuery());

        return $this;
    }

    public function setDomain ($domain) : self {
        $this->update($this->getProtocol(), $domain, $this->getQuery());

        return $this;
    }

    public function setQuery ($query,
                              $encodeMode = self::ENCODE_RFC3986_SLASH_EXCEPTED
    ) : self {
        $this->queryEncoding = $encodeMode;
        $this->update($this->getProtocol(), $this->getDomain(), $query);

        return $this;
    }

    private function isRootQuery($query) : bool {
        return $this->queryEncoding !== self::ENCODE_RFC3986_PURE
               && $query !== ""
               && $query[0] === '/';
    }

    private function update (string $protocol, string $domain, string $query) : self {
        $url = "";

        if ($domain !== "") {
            if ($protocol !== "") {
                $url = $protocol;
            }

            $url .= "://" . self::normalizeDomain($domain);

            if (!$this->isRootQuery($query)) {
                $url .= "/";
            }
        }

        $url .= $this->normalizeQuery($query);

        $this->url = $url;

        return $this;
    }

    public function normalizeQuery (string $query) : string {
        switch ($this->queryEncoding) {
            case self::ENCODE_RFC3986_SLASH_EXCEPTED:
                return (new OmniString($query))
                    ->explode("/")
                    ->map("rawurlencode")
                    ->implode("/")
                    ->__toString();

            case self::ENCODE_AS_IS:
                return $query;

            case self::ENCODE_RFC3986_PURE:
                return rawurlencode($query);
        }

        throw new \Exception('Unexpected encoding value');
    }

    public function beautifyQuery (string $query) : string {
        switch ($this->queryEncoding) {
            case self::ENCODE_RFC3986_SLASH_EXCEPTED:
                return (new OmniString($query))
                    ->explode("/")
                    ->map("rawurldecode")
                    ->implode("/")
                    ->__toString();

            case self::ENCODE_AS_IS:
                return $query;

            case self::ENCODE_RFC3986_PURE:
                return rawurldecode($query);
        }

        throw new \Exception('Unexpected encoding value');
    }

    public static function normalizeDomain (string $domain) : string {
        return \idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46);
    }

    public static function beautifyDomain (string $domain) : string {
        return \idn_to_utf8($domain, 0, INTL_IDNA_VARIANT_UTS46);
    }

    public function __toString () {
        return $this->url;
    }

}
