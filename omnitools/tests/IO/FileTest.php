<?php

namespace Keruald\OmniTools\Tests\IO;

use Keruald\OmniTools\IO\File;
use Keruald\OmniTools\OS\CurrentOS;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

use TypeError;

class FileTest extends TestCase {

    ///
    /// Tests
    ///
    #[DataProvider('provideFilesAndDirectories')]
    public function testGetDirectory (string $filename, string $expected) : void {
        if (CurrentOS::isPureWindows()) {
            $this->markTestSkipped("This test is intended for UNIX systems.");
        }

        $this->assertSame($expected, File::from($filename)->getDirectory());
    }

    #[DataProvider('provideFilesAndFileNames')]
    public function testGetFileName (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getFileName());
    }

    #[DataProvider('provideFilesAndFileNamesWithoutExtension')]
    public function testGetFileNameWithoutExtension (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getFileNameWithoutExtension());
    }

    #[DataProvider('provideFilesAndExtensions')]
    public function testGetExtension (string $filename, string $expected) : void {
        $this->assertSame($expected, File::from($filename)->getExtension());
    }

    ///
    /// Issues
    ///

    /**
     * @see https://devcentral.nasqueron.org/D2494
     */
    public function testNullPathIsNotAllowed () : void {
        $this->expectException(TypeError::class);

        $file = new File(null);
    }

    ///
    /// Data providers
    ///

    public static function provideFilesAndDirectories () : iterable {
        yield ['', ''];
        yield ['/', '/'];
        yield ['/foo', '/'];
        yield ['foo/bar', 'foo'];
        yield ['foo/', '.'];
        yield ['/full/path/to/foo.php', '/full/path/to'];
    }

    public static function provideFilesAndFileNames () : iterable {
        yield ['', ''];
        yield ['foo', 'foo'];
        yield ['foo', 'foo'];
        yield ['foo.php', 'foo.php'];
        yield ['/full/path/to/foo.php', 'foo.php'];
    }

    public static function provideFilesAndFileNamesWithoutExtension () : iterable {
        yield ['', ''];
        yield ['foo', 'foo'];
        yield ['foo.php', 'foo'];
        yield ['/full/path/to/foo.php', 'foo'];
        yield ['foo.tar.gz', 'foo.tar'];

    }

    public static function provideFilesAndExtensions () : iterable {
        yield ['', ''];
        yield ['foo', ''];
        yield ['foo.php', 'php'];
        yield ['/full/path/to/foo.php', 'php'];
        yield ['foo.tar.gz', 'gz'];

    }

}
