<?php declare(strict_types=1);

namespace phpDocumentor\Parser;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemFile
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemFileTest extends TestCase
{
    /**
     * @covers ::path()
     */
    public function testFileCanBeInstantiatedAndPathIsReturned()
    {
        $path = '/path/to/file';
        $file = new FlySystemFile($this->prophesize(FilesystemInterface::class)->reveal(), $path);

        $this->assertSame($path, $file->path());
    }

    /**
     * @covers ::getContents
     */
    public function testContentsOfFileCanBeRetrieved()
    {
        $path = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame($contents, $file->getContents());
    }

    /**
     * @covers ::md5
     */
    public function testGetHashForFile()
    {
        $path = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame(md5($contents), $file->md5());
    }
}
