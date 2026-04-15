<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Parser;

use phpDocumentor\FileSystem\FileSystem;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function md5;

/** @coversDefaultClass \phpDocumentor\Parser\FlySystemFile */
final class FlySystemFileTest extends TestCase
{
    use ProphecyTrait;

    public function testFileCanBeInstantiatedAndPathIsReturned(): void
    {
        $path = '/path/to/file';
        $file = new FlySystemFile($this->prophesize(FileSystem::class)->reveal(), $path);

        $this->assertSame($path, $file->path());
    }

    public function testContentsOfFileCanBeRetrieved(): void
    {
        $path     = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame($contents, $file->getContents());
    }

    public function testGetHashForFile(): void
    {
        $path     = '/path/to/file';
        $contents = 'contents';

        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->read($path)->willReturn($contents);

        $file = new FlySystemFile($fileSystem->reveal(), $path);

        $this->assertSame(md5($contents), $file->md5());
    }
}
