<?php

namespace Keruald\GitHub;

class XHubSignature256 extends XHubSignature {

    const string DEFAULT_ALGO = "sha256";

    /**
     * Initializes a new instance of the XHubSignature256 class
     *
     * @param string $secret the secret token
     * @param string $algo the hash algorithm [facultative]
     */
    public function __construct ($secret, $algo = self::DEFAULT_ALGO) {
        parent::__construct($secret, $algo);
    }

    ///
    /// Static helper methods
    ///

    /**
     * Computes a signature for the specified secret and payload
     *
     * @param string $secret the secret token to secure messages
     * @param string $payload the payload
     * @param string $algo the hash algorithm [facultative]
     *
     * @return string the payload signature
     */
    public static function hashPayload(
        $secret,
        $payload,
        $algo = self::DEFAULT_ALGO,
    ) {
        $instance = new static($secret, $algo);
        $instance->payload = $payload;

        return $instance->compute();
    }

    /**
     * Validates a payload against specified secret
     *
     * @param string $secret the secret token to secure messages
     * @param string $payload the payload
     * @param string $signature the signature delivered with the payload
     * @param string $algo the hash algorithm [facultative]
     *
     * @return bool true if the signature is correct; otherwise, false.
     */
    public static function validatePayload (
        $secret,
        $payload,
        $signature,
        $algo = self::DEFAULT_ALGO,
    ) {
        $instance = new static($secret, $algo);
        $instance->payload = $payload;
        $instance->signature = $signature;

        return $instance->validate();
    }

}
