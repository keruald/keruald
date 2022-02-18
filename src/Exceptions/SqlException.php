<?php

namespace Keruald\Database\Exceptions;

use RuntimeException;
use Exception;

class SqlException extends RuntimeException {

    ///
    /// Constants
    ///

    protected const DEFAULT_MESSAGE =
        "An exception occurred during a database operation.";

    ///
    /// Properties
    ///

    /**
     * @var string The query run when the error occurred.
     */
    public readonly string $query;

    /**
     * A context representing the state of the database engine and the error.
     *
     * @var string[]
     */
    public readonly array $state;

    ///
    /// Constructors
    ///

    private function __construct (
        ?Exception $innerException = null,
        string $query = '',
        array $state = [],
    ) {
        $this->query = $query;
        $this->state = $state;

        if ($innerException !== null) {
            // Build from exception
            parent::__construct(
                $innerException->getMessage(),
                $innerException->getCode(),
                $innerException,
            );
        } else {
            // Build from state
            parent::__construct(
                $this->state['error'] ?? self::DEFAULT_MESSAGE,
                $this->state['errno'] ?? 0,
            );
        }
    }

    /**
     * Normalize a SQL exception thrown by a PHP database extension.
     *
     * @param Exception $innerException
     * @param string $query
     * @param array $context
     * @return static
     */
    public static function fromException (
        Exception $innerException,
        string $query,
        array $context
    ) : self {
        return new self($innerException, $query, $context);
    }

    /**
     * Create a SQL exception from a query and a context.
     *
     * @param string $query
     * @param array $context
     * @return static
     */
    public static function fromQuery (string $query, array $context) : self {
        return new self(null, $query, $context);
    }

}
