<?php

namespace Keruald\OmniTools\Tests\IO;

use Keruald\OmniTools\IO\FileUtilities;
use PHPUnit\Framework\TestCase;

class FileUtilitiesTest extends TestCase {

    public function testGetExtension () : void {
        $this->assertSame("jpg", FileUtilities::getExtension("image.jpg"));
    }

}
