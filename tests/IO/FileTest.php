<?php

namespace Keruald\OmniTools\Tests\IO;

use Keruald\OmniTools\IO\File;
use PHPUnit\Framework\TestCase;

class FileTest extends TestCase {

    ///
    /// Tests
    ///

    /**
     * @dataProvider provideFilesAndDirectories
     */
    public function testGetDirectory (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getDirectory());
    }

    /**
     * @dataProvider provideFilesAndFileNames
     */
    public function testGetFileName (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getFileName());
    }

    /**
     * @dataProvider provideFilesAndFileNamesWithoutExtension
     */
    public function testGetFileNameWithoutExtension (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getFileNameWithoutExtension());
    }

    /**
     * @dataProvider provideFilesAndExtensions
     */
    public function testGetExtension (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getExtension());
    }

    ///
    /// Data providers
    ///

    public function provideFilesAndDirectories () : iterable {
        yield ['', ''];
        yield ['/', '/'];
        yield ['/foo', '/'];
        yield ['foo/bar', 'foo'];
        yield ['foo/', '.'];
        yield ['/full/path/to/foo.php', '/full/path/to'];
    }

    public function provideFilesAndFileNames () : iterable {
        yield ['', ''];
        yield ['foo', 'foo'];
        yield ['foo', 'foo'];
        yield ['foo.php', 'foo.php'];
        yield ['/full/path/to/foo.php', 'foo.php'];
    }

    public function provideFilesAndFileNamesWithoutExtension () : iterable {
        yield ['', ''];
        yield ['foo', 'foo'];
        yield ['foo.php', 'foo'];
        yield ['/full/path/to/foo.php', 'foo'];
        yield ['foo.tar.gz', 'foo.tar'];

    }

    public function provideFilesAndExtensions () : iterable {
        yield ['', ''];
        yield ['foo', ''];
        yield ['foo.php', 'php'];
        yield ['/full/path/to/foo.php', 'php'];
        yield ['foo.tar.gz', 'gz'];

    }

}
