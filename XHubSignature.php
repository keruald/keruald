<?php
namespace Keruald\GitHub;

class XHubSignature {

    ///
    /// Properties
    ///

    /**
     * The secret token to secure messages
     *
     * @var string
     */
    private $secret;

    /**
     * The hash algorithm
     *
     * @var string
     */
    private $hashAlgo;

    /**
     * The payload
     *
     * @var string
     */
    public $payload;

    /**
     * The signature delivered with the payload, to validate it
     *
     * @var string
     */
    public $signature;

    ///
    /// Constants
    ///

    /**
     * The default hash algorithm to use if none is offered
     */
    const DEFAULT_HASH_ALGO = 'sha1';

    ///
    /// Constructor
    ///

    /**
     * Initializes a new instance of the XHubSignature class
     *
     * @param string $secret the secret token
     * @param string $algo the algorithm to use to compute hashs [facultative]
     */
    public function __construct ($secret, $algo = DEFAULT_HASH_ALGO) {
        $this->secret = $secret;
        $this->hashAlgo = $algo;
    }

    ///
    /// Signature methods
    ///

    /**
     * Computes the signature for the current payload
     *
     * @return string the payload signature
     */
    public function compute () {
        return hash_hmac($this->algo, $this->payload, $this->secret);
    }

    /**
     * Validates the signature
     *
     * @return bool true if the signature is correct; otherwise, false.
     */
    public function validate () {
        // Comparison with hash_equals allows to mitigate timing attacks.
        return hash_equals($this->compute(), $this->signature);
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
        $algo = DEFAULT_HASH_ALGO
    ) {
        $instance = new static($secret, $algo = DEFAULT_HASH_ALGO);
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
        $algo = DEFAULT_HASH_ALGO
    ) {
        $instance = new static($secret, $algo = DEFAULT_HASH_ALGO);
        $instance->payload = $payload;
        $instance->signature = $signature;

        return $instance->validate();
    }
}
