<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests;

trait WithData {

    protected function getDataPath (string $file) : string {
        return $this->getDataDirectory() . "/" . $file;
    }

    protected function getDataDirectory () : string {
        return __DIR__ . "/data";
    }

}
