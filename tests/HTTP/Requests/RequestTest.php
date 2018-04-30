<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\HTTP\Requests;

use Keruald\OmniTools\HTTP\Requests\Request;
use PHPUnit\Framework\TestCase;

class RequestTest extends TestCase {

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\Request::getRemoteAddress
     * @backupGlobals enabled
     */
    public function testGetRemoteAddress () : void {
        $this->assertEmpty(Request::getRemoteAddress());

        $_SERVER = [
            'REMOTE_ADDR' => '10.0.0.2',
        ];
        $this->assertEquals('10.0.0.2', Request::getRemoteAddress());

        $_SERVER += [
            'HTTP_X_FORWARDED_FOR' => '10.0.0.3',
            'HTTP_CLIENT_IP' => '10.0.0.4',
        ];
        $this->assertEquals(
            '10.0.0.3', Request::getRemoteAddress(),
            "HTTP_X_FORWARDED_FOR must be prioritized."
        );
    }

    /**
     * @covers \Keruald\OmniTools\HTTP\Requests\Request::getAcceptedLanguages
     * @backupGlobals enabled
     */
    public function testGetAcceptedLanguages () : void {
        $_SERVER = [
            'HTTP_ACCEPT_LANGUAGE' => 'fr,en-US;q=0.7,en;q=0.3',
        ];

        $this->assertEquals(
            ["fr", "en-US", "en"],
            Request::getAcceptedLanguages()
        );
    }

}
