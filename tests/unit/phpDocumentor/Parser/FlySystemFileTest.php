<?php

declare(strict_types=1);

namespace phpDocumentor\Parser;

use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use function md5;

/**
 * @coversDefaultClass \phpDocumentor\Parser\FlySystemFile
 * @covers ::__construct
 * @covers ::<private>
 */
final class FlySystemFileTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::path()
     */
    public function testFileCanBeInstantiatedAndPathIsReturned() : void
    {
        $path = '/path/to/file';
        $file = new FlySystemFile($this->prophesize(FilesystemInterface::class)->reveal(), $path);

        $this->assertSame($path, $file->path());
    }

    /**
     * @covers ::getContents
     */
    public function testContentsOfFileCanBeRetrieved() : void
    {
        $path     = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame($contents, $file->getContents());
    }

    /**
     * @covers ::md5
     */
    public function testGetHashForFile() : void
    {
        $path     = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame(md5($contents), $file->md5());
    }
}
