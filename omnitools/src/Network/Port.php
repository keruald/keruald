<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

class Port {

    ///
    /// Properties
    ///

    public Protocol $protocol;

    public int $number;

    ///
    /// Constructor
    ///

    public function __construct (int $number, Protocol $protocol = Protocol::TCP) {
        $this->number = $number;
        $this->protocol = $protocol;
    }

}
