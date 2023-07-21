<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use League\Flysystem\FileNotFoundException;
use League\Flysystem\FilesystemInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Twig\Error\LoaderError;
use Twig\Source;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\FlySystemLoader
 * @covers ::<private>
 * @covers ::__construct
 */
final class FlySystemLoaderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::exists
     * @dataProvider fileProvider
     */
    public function testExists(string $fileName, string $resolvedFileName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->has($resolvedFileName)->willReturn(true);

        $loader = new FlySystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertTrue($loader->exists($fileName));
    }

    /**
     * @covers ::getSourceContext
     * @dataProvider fileProvider
     */
    public function testGetSourceContext(string $fileName, string $resolvedFileName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata($resolvedFileName)->willReturn(['type' => 'file']);
        $fileSystem->read($resolvedFileName)->willReturn('content');

        $loader = new FlySystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertEquals(
            new Source(
                'content',
                $fileName,
                $resolvedFileName,
            ),
            $loader->getSourceContext($fileName),
        );
    }

    /**
     * @covers ::getCacheKey
     * @dataProvider fileProvider
     */
    public function testGetCacheKey(string $fileName, string $resolvedFileName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata($resolvedFileName)->willReturn(['type' => 'file']);

        $loader = new FlySystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertSame($fileName, $loader->getCacheKey($fileName));
    }

    /**
     * @covers ::isFresh
     * @dataProvider fileProvider
     */
    public function testIsFresh(string $fileName, string $resolvedFileName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata($resolvedFileName)->willReturn(['type' => 'file']);
        $fileSystem->getTimestamp($resolvedFileName)->willReturn(10);

        $loader = new FlySystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertTrue($loader->isFresh($fileName, 10));
    }

    public function fileProvider(): array
    {
        return [
            'normal file' => [
                'filename' => 'myTwigFile',
                'resolvedName' => 'myTwigFile',
                'overloadPrefix' => null,
            ],
            'normal file with prefix' => [
                'filename' => 'myTwigFile',
                'resolvedName' => 'myTwigFile',
                'overloadPrefix' => 'base',
            ],
            'prefixed file with prefix' => [
                'filename' => 'base::myTwigFile',
                'resolvedName' => 'myTwigFile',
                'overloadPrefix' => 'base',
            ],
        ];
    }

    public function testInvalidFileType(): void
    {
        $this->expectException(LoaderError::class);
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata('someDir')->willReturn(['type' => 'dir']);

        $loader = new FlySystemLoader($fileSystem->reveal(), '');
        $loader->getSourceContext('someDir');
    }

    public function testFileDoesNotExist(): void
    {
        $this->expectException(LoaderError::class);
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata('someDir')->willThrow(new FileNotFoundException('someDir'));

        $loader = new FlySystemLoader($fileSystem->reveal(), '');
        $loader->getSourceContext('someDir');
    }

    public function testLoadFileWithPrefix(): void
    {
        $fileSystem = $this->prophesize(FilesystemInterface::class);
        $fileSystem->getMetadata('test/file.twig')->willReturn(['type' => 'file']);
        $fileSystem->read('test/file.twig')->willReturn('content');

        $loader = new FlySystemLoader($fileSystem->reveal(), 'test/');
        $this->assertEquals(
            new Source(
                'content',
                'file.twig',
                'test/file.twig',
            ),
            $loader->getSourceContext('file.twig'),
        );
    }
}
