<?php
declare(strict_types=1);

namespace Keruald\OmniTools\IO;

class FileUtilities {

    public static function getExtension (string $filename) : string {
        return File::from($filename)->getExtension();
    }

}
