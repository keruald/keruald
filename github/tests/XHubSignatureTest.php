<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use Keruald\GitHub\XHubSignature;

require 'XHubSignatureConstants.php';

class XHubSignatureTest extends TestCase {
    protected $defaultInstance;
    protected $tigerInstance;

    protected function setUp() : void {
        $this->defaultInstance = new XHubSignature(SECRET);
        $this->tigerInstance = new XHubSignature(SECRET, TIGER_ALGO);

        $this->defaultInstance->payload = DEFAULT_PAYLOAD;
        $this->tigerInstance->payload = TIGER_PAYLOAD;
    }

    public function testValidate () : void {
        $this->defaultInstance->signature = "";
        $this->assertFalse($this->defaultInstance->validate());

        $this->defaultInstance->signature = "bad signature";
        $this->assertFalse($this->defaultInstance->validate());

        $this->defaultInstance->signature = DEFAULT_SIGNATURE;
        $this->assertTrue($this->defaultInstance->validate());
    }

    public function testCompute () : void {
        $this->assertSame(
            DEFAULT_SIGNATURE,
            $this->defaultInstance->compute()
        );

        $this->assertSame(
            TIGER_SIGNATURE,
            $this->tigerInstance->compute()
        );
    }

    ///
    /// Test static helper methods
    ///

    #[CoversFunction(XHubSignature::validatePayload)]
    public function testhashPayload () : void {
        $this->assertSame(
            EMPTY_DEFAULT_HASH_ALGO_SIGNATURE,
            XHubSignature::hashPayload("", "")
        );
        $this->assertSame(
            TIGER_SIGNATURE,
            XHubSignature::hashPayload(SECRET, TIGER_PAYLOAD, TIGER_ALGO)
        );
    }

    #[CoversFunction(XHubSignature::validatePayload)]
    public function testValidatePayload () : void {
        $this->assertFalse(XHubSignature::validatePayload("", "", ""));

        $this->assertTrue(XHubSignature::validatePayload(
            SECRET,
            TIGER_PAYLOAD,
            TIGER_SIGNATURE,
            TIGER_ALGO
        ));
    }

    public function testParseSignature () : void {
        $this->assertSame(
            TIGER_SIGNATURE,
            XHubSignature::parseSignature(TIGER_SIGNATURE)
        );
        $this->assertSame(
            TIGER_SIGNATURE,
            XHubSignature::parseSignature(TIGER_ALGO . '=' . TIGER_SIGNATURE)
        );
    }
}
