<?php

namespace Keruald\OmniTools\Tests\IO;

use Keruald\OmniTools\IO\Directory;
use Keruald\OmniTools\IO\File;

use Keruald\OmniTools\Tests\WithData;
use PHPUnit\Framework\TestCase;

class DirectoryTest extends TestCase {

    use WithData;

    const TEST_DIRECTORY = "MockLib";

    private string $path;
    private Directory $directory;

    protected function setUp () : void {
        $this->path = $this->getDataPath(self::TEST_DIRECTORY);
        $this->directory = new Directory($this->path);
    }

    public function testGlob () : void {
        $expected = [
            new File($this->path . '/Bar.php'),
            new File($this->path . '/Foo.php'),
        ];
        $actual = $this->directory->glob("*.php");

        $this->assertEquals($expected, $actual);
    }

    public function testIsReadable () : void {
        $this->assertTrue($this->directory->isReadable());
    }

    public function testIsWritable () : void {
        $result = tempnam($this->path, "test-write-");

        if ($result === false) {
            $this->markTestSkipped("Can't test write operation.");
        }

        $canWrite = str_starts_with($result, $this->path);
        unlink($result);

        $this->assertSame($canWrite, $this->directory->isWritable());
    }

    public function testExists () : void {
        $this->assertTrue($this->directory->exists());
    }

    public function testExistsWhenItDoesNot () : void {
        $directory = new Directory("/nonexistent");
        $this->assertFalse($directory->exists());
    }

    public function testExistsWhenItMatchesFile () : void {
        $path = $this->getDataPath(self::TEST_DIRECTORY . "/Foo.php");
        $directory = new Directory($path);

        $this->assertFalse($directory->exists());
    }

    public function testSetPath () : void {
        $this->directory->setPath("/bar/foo");
        $this->assertEquals("/bar/foo", $this->directory->getPath());
    }

    public function testGetPath () : void {
        $this->assertEquals($this->path, $this->directory->getPath());
    }

    public function testGetDirectoryName () : void {
        $actual = $this->directory->getDirectoryName();
        $this->assertEquals(self::TEST_DIRECTORY, $actual);
    }

    public function testGetPathInfo () : void {
        $expected = [
            'dirname' => $this->getDataDirectory(),
            'basename' => self::TEST_DIRECTORY,
            'filename' => self::TEST_DIRECTORY,
        ];

        $this->assertEquals($expected, $this->directory->getPathInfo());
    }

    public function testGetParentDirectory () : void {
        $actual = $this->directory->getParentDirectory();
        $this->assertEquals($this->getDataDirectory(), $actual);
    }

}
