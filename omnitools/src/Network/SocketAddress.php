<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

use RuntimeException;

class SocketAddress {

    ///
    /// Constants
    ///

    const DEFAULT_TIMEOUT = 10;

    const PROTOCOL_TCP = 6; // getprotobyname("tcp")

    ///
    /// Properties
    ///

    public IP $address;

    public Port $port;

    ///
    /// Constructors
    ///

    public function __construct (IP $address,  Port $port) {
        $this->address = $address;
        $this->port = $port;
    }

    /**
     * Gets new socket address from specified IP, port and protocol information.
     *
     * This method takes care to creates the underlying IP and Port object.
     */
    public static function from (
        string $ip,
        int $port, Protocol $protocol = Protocol::TCP
    ) : self {
        return new self(
            IP::from($ip),
            new Port($port, $protocol),
        );
    }

    ///
    /// Interact with socket
    ///

    /**
     * Determines if a TCP socket is open
     *
     * @throws RuntimeException if the protocol isn't TCP
     * @return bool if the TCP socket accepts a connection
     */
    public function isOpen() : bool {
        if ($this->port->protocol !== Protocol::TCP) {
            throw new RuntimeException("Check if a port is open is only implemented for TCP.");
        }

        if (!function_exists("socket_create")) {
            return $this->isOpenLegacy();
        }

        $socket = socket_create($this->address->getDomain(), SOCK_STREAM, self::PROTOCOL_TCP);
        $result = socket_connect($socket, $this->address->__toString(), $this->port->number);
        socket_close($socket);

        return $result;
    }

    private function isOpenLegacy () : bool {
        $fp = @fsockopen(
            $this->address->__toString(),
            $this->port->number,
            $errorCode, $errorMessage,
            self::DEFAULT_TIMEOUT
        );

        if ($fp) {
            fclose($fp);

            return true;
        }

        return false;
    }

}
