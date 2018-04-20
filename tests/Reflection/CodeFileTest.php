<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\Reflection\CodeFile;
use Keruald\OmniTools\Tests\WithData;
use PHPUnit\Framework\TestCase;

use Acme\MockLib\Bar; // a mock class in a mock namespace to test include

class CodeFileTest extends TestCase {

    use WithData;

    /**
     * @var CodeFile
     */
    private $validCodeFile;

    /**
     * @var CodeFile
     */
    private $notExistingCodeFile;

    public function setUp () : void {
        $file = $this->getDataPath("MockLib/Bar.php");
        $this->validCodeFile = CodeFile::from($file);

        $this->notExistingCodeFile = CodeFile::from("/notexisting");
    }

    public function testCanInclude () : void {
        $this->assertTrue($this->validCodeFile->canBeIncluded());
        $this->assertFalse($this->notExistingCodeFile->canBeIncluded());
    }

    public function testTryInclude () : void {
        $this->assertTrue($this->validCodeFile->tryInclude());
        $this->assertTrue(class_exists(Bar::class));

        $this->assertFalse($this->notExistingCodeFile->tryInclude());
    }

}
