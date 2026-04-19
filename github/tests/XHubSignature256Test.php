<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Keruald\GitHub\XHubSignature256;

require 'XHubSignatureConstants.php';

class XHubSignature256Test extends TestCase {
    protected XHubSignature256 $instance;

    protected function setUp() : void {
        $this->instance = new XHubSignature256(DEFAULT_256_SECRET);

        $this->instance->payload = DEFAULT_256_PAYLOAD;
    }

    ///
    /// Tests
    ///

    public function testValidate() : void {
        $this->instance->signature = DEFAULT_256_SIGNATURE;
        $this->assertTrue($this->instance->validate());
    }

    ///
    /// Test static helper methods
    ///

    public function testValidatePayload() : void {
        $this->assertTrue(XHubSignature256::validatePayload(
            DEFAULT_256_SECRET,
            DEFAULT_256_PAYLOAD,
            DEFAULT_256_SIGNATURE
        ));
    }

}
