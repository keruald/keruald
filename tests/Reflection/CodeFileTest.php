<?php
declare(strict_types=1);

namespace Keruald\OmniTools\Tests\Reflection;

use Keruald\OmniTools\OS\CurrentOS;
use Keruald\OmniTools\OS\CurrentProcess;
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

    public function testCanBeIncludedWhenFileModeForbidsReading () : void {
        if (CurrentOS::isPureWindows()) {
            $this->markTestSkipped("This test is intended for UNIX systems.");
        }

        if (CurrentProcess::isPrivileged()) {
            $this->markTestSkipped(
                "This test requires non-root access to run properly."
            );
        }

        $file = $this->getNonReadableFile();

        $this->assertFalse(CodeFile::From($file)->canBeIncluded());

        unlink($file);
    }

    public function testTryInclude () : void {
        $this->assertTrue($this->validCodeFile->tryInclude());
        $this->assertTrue(class_exists(Bar::class));

        $this->assertFalse($this->notExistingCodeFile->tryInclude());
    }

    public function testIsReadable () : void {
        $this->assertTrue($this->validCodeFile->isReadable());
    }

    public function testIsReadableWhenFileModeForbidsReading () : void {
        if (CurrentOS::isPureWindows()) {
            $this->markTestSkipped("This test is intended for UNIX systems.");
        }

        if (CurrentProcess::isPrivileged()) {
            $this->markTestSkipped(
                "This test requires non-root access to run properly."
            );
        }

        $file = $this->getNonReadableFile();

        $this->assertFalse(CodeFile::From($file)->isReadable());

        unlink($file);
    }

    private function getNonReadableFile () : string {
        $file = tempnam(sys_get_temp_dir(), "testCodeFile");
        chmod($file, 0);

        return $file;
    }

}
