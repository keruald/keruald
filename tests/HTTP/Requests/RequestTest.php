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

}
