<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Collections\HashMap;
use Keruald\OmniTools\DateTime\DateStamp;
use Keruald\OmniTools\HTTP\Requests\Request;

class AcmeApplication {

    public function __construct (
        private Request $request,
        private HashMap $session,
        private DateStamp $dateStamp,
        private int $counter,
        private array $inventory,
        private float $temperature,
        private bool $isSecure,
    ) {
    }

    public function getDateStamp () : DateStamp {
        return $this->dateStamp;
    }

}
