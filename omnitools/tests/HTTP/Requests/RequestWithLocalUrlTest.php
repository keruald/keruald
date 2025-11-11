<?php

namespace Keruald\OmniTools\Tests\HTTP\Requests;

use Keruald\OmniTools\HTTP\Requests\Request;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class RequestWithLocalUrlTest extends TestCase {

    private Request $request;

    protected function setUp() : void {
        $this->request = new Request();
    }

    ///
    /// Data providers
    ///

    /**
     * Provides equivalent base URLs for testing code handles trailing slash
     */
    public static function provideBaseUrls () : iterable {
        yield ["/tracker"];
        yield ["/tracker/"];
    }

    public static function provideUrlFragments () : iterable {
        yield [[""], "/"];
        yield [["foo"], "/foo"];
        yield [["foo", "bar"], "/foo/bar"];
    }

    public static function provideBaseUrlsAndUrlFragments () : iterable {
        yield ["/tracker", [], "/tracker"]; // respects base URL exact value
        yield ["/tracker", [""], "/tracker"]; // respects base URL exact value
        yield ["/tracker", ["foo"], "/tracker/foo"];
        yield ["/tracker", ["foo", "bar"], "/tracker/foo/bar"];

        yield ["/tracker/", [], "/tracker/"]; // respects base URL exact value
        yield ["/tracker/", [""], "/tracker/"];  // respects base URL exact value
        yield ["/tracker/", ["foo"], "/tracker/foo"];
        yield ["/tracker/", ["foo", "bar"], "/tracker/foo/bar"];
    }

    ///
    /// Tests for buildUrl()
    ///

    #[dataProvider("provideUrlFragments")]
    public function testBuildUrl($parts, $url) : void {
        $this->assertEquals($url, $this->request->buildUrl(...$parts));
    }

    #[dataProvider("provideBaseUrlsAndUrlFragments")]
    public function testBuildUrlWithBaseUrl($baseUrl, $parts, $url) : void {
        $this->request
            ->withSiteUrl("http://localhost")
            ->withBaseUrl("$baseUrl");

        $this->assertEquals($url, $this->request->buildUrl(...$parts));
    }

    ///
    /// Tests for getCurrentUrl() scenarii
    ///

    public function testGetCurrentUrlWithPathInfo () : void {
        $_SERVER["PATH_INFO"] = "/foo";

        $this->assertEquals("/foo", $this->request->getCurrentUrl());
    }

    public function testGetCurrentUrlRegularRequestCase () : void {
        $_SERVER["REQUEST_URI"] = "/foo";

        $this->assertEquals("/foo", $this->request->getCurrentUrl());
    }

    public function testGetCurrentUrlRequestCaseWithQueryString () : void {
        $_SERVER["REQUEST_URI"] = "/foo?a=b";
        $_SERVER["QUERY_STRING"] = "a=b";

        $this->assertEquals("/foo", $this->request->getCurrentUrl());
    }

    public function testGetCurrentUrlWithRedirect () : void {
        // Let's redirect /T42 to /tasks/legacy/42
        // Controller needs /tasks/legacy/42 to parse route
        $_SERVER["REQUEST_URI"] = "/T42";
        $_SERVER["REDIRECT_URL"] = "/tasks/legacy/42";

        $this->assertEquals("/tasks/legacy/42", $this->request->getCurrentUrl());
    }

    #[DataProvider("provideBaseUrls")]
    public function testGetCurrentUrlWithRedirectAndCustomEntryPoint ($baseUrl) : void {
        // Site root is https://site.domain.tld/tracker/
        // Let's redirect /T42 to /tracker/tasks/legacy/42
        // Controller still needs /tasks/legacy/42 to parse route
        $_SERVER["REQUEST_URI"] = "/T42";
        $_SERVER["REDIRECT_URL"] = "/tracker/tasks/legacy/42";

        $this->request
            ->withSiteUrl("http://localhost")
            ->withBaseUrl("$baseUrl");

        $this->assertEquals("/tasks/legacy/42", $this->request->getCurrentUrl());
    }

    #[DataProvider("provideBaseUrls")]
    public function testGetCurrentUrlRegularRequestCaseWithBaseUrl ($baseUrl) : void {
        $_SERVER["REQUEST_URI"] = "/tracker/tasks/legacy/42";

        $this->request
            ->withSiteUrl("http://localhost")
            ->withBaseUrl("$baseUrl");

        $this->assertEquals("/tasks/legacy/42", $this->request->getCurrentUrl());
    }

    ///
    /// Tests for getCurrentUrlFragments() scenarii
    ///

    #[DataProvider("provideBaseUrls")]
    public function testGetCurrentUrlFragments ($baseUrl) : void {
        // This case has arbitrarily been picked, as this helper function
        // first call getCurrentUrl(). It's not needed to browse again all tbe cases.
        $_SERVER["REQUEST_URI"] = "/tracker/tasks/legacy/42";

        $this->request
            ->withSiteUrl("http://localhost")
            ->withBaseUrl("$baseUrl");

        $this->assertEquals(["tasks", "legacy", "42"], $this->request->getCurrentUrlFragments());
    }

    public function testGetCurrentUrlFragmentsWhenSourceMatchesPhpSelf () : void {
        $_SERVER["REQUEST_URI"] = "/index.php";
        $_SERVER["PHP_SELF"] = "/index.php";

        $this->assertEquals([], $this->request->getCurrentUrlFragments());
    }

}
