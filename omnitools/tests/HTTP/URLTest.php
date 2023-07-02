<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\HTTP;

use Keruald\OmniTools\HTTP\URL;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class URLTest extends TestCase {

    #[DataProvider('provideURLsAndComponents')]
    public function testGetDomain ($url, $expectedDomain) : void {
        $url = new URL($url);

        $this->assertEquals($expectedDomain, $url->getDomain());
    }

    #[DataProvider('provideURLsAndComponents')]
    public function testGetProtocol ($url, $_, $expectedProtocol) : void {
        $url = new URL($url);

        $this->assertEquals($expectedProtocol, $url->getProtocol());
    }

    #[DataProvider('provideURLsAndComponents')]
    public function testGetQuery ($url, $_, $__, $expectedQuery) : void {
        $url = new URL($url);

        $this->assertEquals($expectedQuery, $url->getQuery());
    }

    public function testSetProtocol () : void {
        $url = new URL("https://acme.tld/foo");
        $url->setProtocol("xizzy");

        $this->assertEquals("xizzy", $url->getProtocol());
    }

    public function testSetDomain () : void {
        $url = new URL("https://acme.tld/foo");
        $url->setDomain("xizzy");

        $this->assertEquals("xizzy", $url->getDomain());
    }

    public function testSetQuery () : void {
        $url = new URL("https://acme.tld/foo");
        $url->setQuery("/xizzy");

        $this->assertEquals("/xizzy", $url->getQuery());
    }

    public function testSetQueryWithSlashForgotten () : void {
        $url = new URL("https://acme.tld/foo");
        $url->setQuery("xizzy");

        $this->assertEquals("/xizzy", $url->getQuery());
    }

    #[DataProvider('provideURLsAndComponents')]
    public function testCompose ($url, $domain, $protocol, $query,
                                 $expectedUrl = null) {
        $this->assertEquals(
            $expectedUrl ?? $url,
            URL::compose($protocol, $domain, $query)->__toString()
        );
    }

    public static function provideURLsAndComponents () : iterable {
        // base URL, domain, protocol, query[, expected URL]
        // When omitted, the expected URL is the base URL.

        yield ["http://foo/bar", "foo", "http", "/bar"];
        yield ["https://xn--dghrefn-mxa.nasqueron.org/", "dæghrefn.nasqueron.org", "https", "/"];
        yield ["://foo/bar", "foo", "", "/bar"];
        yield ["/bar", "", "", "/bar"];
        yield ["http://foo/bar%20quux", "foo", "http", "/bar quux"];
        yield ["https://foo/", "foo", "https", "/"];
        yield ["https://foo", "foo", "https", "/", "https://foo/"];
    }

}
