<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Network;

enum Protocol {
    case TCP;
    case UDP;
    case SCTP;
    case DCCP;
}
