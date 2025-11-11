<?php

namespace Keruald\OmniTools\HTTP\Requests;

use RuntimeException;

trait WithLocalURL {

    ///
    /// Properties from configuration
    ///

    private string $baseUrl = "";

    private ?string $siteUrl = null;

    ///
    /// Properties
    ///

    private function getSiteUrl () : string {
        return $this->siteUrl ?? Request::getServerURL();
    }

    public function withBaseUrl (string $url) : static {
        $this->baseUrl = $url;

        return $this;
    }

    public function withSiteUrl (string $url) : static {
        $this->siteUrl = $url;

        return $this;
    }

    ///
    /// Methods to get or build URLs
    ///

    /**
     * Gets the URL matching the specified resource.
     *
     * Example:
     * <code>
     * $ship = new Ship();
     * $ship->id = "S00001";
     *
     * $request = new Request();
     * $url = $request->buildUrl("ship", $ship->id);
     * echo $url; // This should print "/ship/S00001"
     * </code>
     *
     * @param string ...$parts The URL parts
     * @return string the URL matching the specified resource
     */
    public function buildUrl (string ...$parts) : string {
        if (self::hasParts($parts)) {
            $baseUrl = $this->baseUrl;
            if (!str_ends_with($baseUrl, "/")) {
                $baseUrl .= "/";
            }

            return $baseUrl . implode("/", $parts);
        }

        if ($this->baseUrl == "" || $this->baseUrl == $_SERVER["PHP_SELF"]) {
            return "/";
        }

        return $this->baseUrl;
    }

    private static function hasParts (array $parts) : bool {
        if (count($parts) == 0) {
            return false;
        }

        if (count($parts) == 1 && $parts[0] == "") {
            return false;
        }

        return true;
    }

    /**
     * Gets $_SERVER["PATH_INFO"] or computes the equivalent if not defined.
     *
     * This function allows the entry point controllers to get the current URL
     * consistently, for any redirection configuration.
     *
     * So with /foo/bar, /index.php/foo/bar, /zed/index.php/foo/bar or /zed/foo/bar
     * `Request::getCurrentUrl()` will return /foo/bar each time.
     *
     * @return string the relevant URL part
     */
    public function getCurrentUrl () : string {
        // CASE 1. PATH_INFO is defined.
        // This is a straightforward case, we just return it, server configuration
        // is responsible for properly cut the URL.
        if (array_key_exists("PATH_INFO", $_SERVER)) {
            return $_SERVER["PATH_INFO"];
        }

        // Useful parts of the URL
        $siteUrl = $this->getSiteUrl();
        $serverUrl = $this->getServerURL();
        $currentUrl = $serverUrl . $_SERVER["REQUEST_URI"];

        $server_len = strlen($serverUrl);
        $len = $server_len + strlen($this->baseUrl); // Relevant URL part starts after the site URL

        // Allow configuration to add an extraneous trailing slash from base URL
        if (str_ends_with($this->baseUrl, "/")) {
            $len--;
        }

        // Throw an exception if the site URL is not the beginning of the URL
        // because in that case, we can't determine where to cut the URL.
        if (substr($currentUrl, 0, $server_len) != $siteUrl) {
            throw new RuntimeException(
                "Site URL mismatch: a value starting by $serverUrl is expected, but got $siteUrl."
            );
        }

        // CASE 2. REDIRECT_URL is defined.
        //
        // A popular legacy configuration is Apache + mod_rewrite
        // to redirect content clean URLs to an entry point script.
        //
        // In that case, we take the part after this entry point script.
        if (array_key_exists("REDIRECT_URL", $_SERVER)) {
            return substr($serverUrl . $_SERVER["REDIRECT_URL"], $len);
        }

        // CASE 3. Use REQUEST_URI but remove QUERY_STRING
        $url = substr($currentUrl, $len);
        if (array_key_exists("QUERY_STRING", $_SERVER) && $_SERVER["QUERY_STRING"] != "") {
            return substr($url, 0, strlen($url) - strlen($_SERVER["QUERY_STRING"]) - 1);
        }

        return $url;
    }

    /**
     * Gets an array of url fragments to be processed by controller
     * @see self::getCurrentUrl()
     *
     * This method is used by the controllers' entry points to know the URL and
     * call relevant subcontrollers.
     *
     * @return string[] an array of string, one for each URL fragment
     */
    public function getCurrentUrlFragments () : array {
        $source = $this->getCurrentUrl();

        if ($source == $_SERVER["PHP_SELF"]) {
            return [];
        }

        return explode("/", substr($source, 1));
    }

}
