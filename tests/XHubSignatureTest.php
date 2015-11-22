<?php

use Keruald\GitHub\XHubSignature;

require 'XHubSignatureConstants.php';

class XHubSignatureTest extends PHPUnit_Framework_TestCase {
    protected $defaultInstance;
    protected $tigerInstance;

    protected function setUp() {
        $this->defaultInstance = new XHubSignature(SECRET);
        $this->tigerInstance = new XHubSignature(SECRET, TIGER_ALGO);

        $this->defaultInstance->payload = DEFAULT_PAYLOAD;
        $this->tigerInstance->payload = TIGER_PAYLOAD;
    }

    public function testValidate () {
        $this->defaultInstance->signature = "";
        $this->assertFalse($this->defaultInstance->validate());

        $this->defaultInstance->signature = "bad signature";
        $this->assertFalse($this->defaultInstance->validate());

        $this->markTestIncomplete(
          'Validation test has not been implemented yet.'
        );
    }

    public function testCompute () {
        $this->assertSame(
            DEFAULT_SIGNATURE,
            $this->defaultInstance->compute()
        );

        $this->assertSame(
            TIGER_SIGNATURE,
            $this->tigerInstance->compute()
        );

        $this->markTestIncomplete(
          'Compute test for default instance should be extracted from a real GitHub payload.'
        );
    }

    ///
    /// Test static helper methods
    ///

    /**
     * @covers XHubSignature::validatePayload
     */
    public function testhashPayload () {
        $this->assertSame(
            EMPTY_DEFAULT_HASH_ALGO_SIGNATURE,
            XHubSignature::hashPayload("", "")
        );
        $this->assertSame(
            TIGER_SIGNATURE,
            XHubSignature::hashPayload(SECRET, TIGER_PAYLOAD, TIGER_ALGO)
        );
    }

    /**
     * @covers XHubSignature::validatePayload
     */
    public function testValidatePayload () {
        $this->assertFalse(XHubSignature::validatePayload("", "", ""));

        $this->assertTrue(XHubSignature::validatePayload(
            SECRET,
            TIGER_PAYLOAD,
            TIGER_SIGNATURE,
            TIGER_ALGO
        ));
    }
}
