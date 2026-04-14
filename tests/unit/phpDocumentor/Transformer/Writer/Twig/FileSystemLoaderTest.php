<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\FileSystem\FileSystem;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Twig\Error\LoaderError;
use Twig\Source;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\FileSystemLoader */
final class FileSystemLoaderTest extends TestCase
{
    use ProphecyTrait;

    /** @dataProvider fileProvider */
    public function testExists(string $filename, string $resolvedName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has($resolvedName)->willReturn(true);

        $loader = new FileSystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertTrue($loader->exists($filename));
    }

    /** @dataProvider fileProvider */
    public function testGetSourceContext(string $filename, string $resolvedName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has($resolvedName)->willReturn(true);
        $fileSystem->read($resolvedName)->willReturn('content');

        $loader = new FileSystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertEquals(
            new Source(
                'content',
                $filename,
                $resolvedName,
            ),
            $loader->getSourceContext($filename),
        );
    }

    /** @dataProvider fileProvider */
    public function testGetCacheKey(string $filename, string $resolvedName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has($resolvedName)->willReturn(true);

        $loader = new FileSystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertSame($filename, $loader->getCacheKey($filename));
    }

    /** @dataProvider fileProvider */
    public function testIsFresh(string $filename, string $resolvedName, string|null $overloadPrefix): void
    {
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has($resolvedName)->willReturn(true);
        $fileSystem->lastModified($resolvedName)->willReturn(10);

        $loader = new FileSystemLoader($fileSystem->reveal(), '', $overloadPrefix);

        $this->assertTrue($loader->isFresh($filename, 10));
    }

    public static function fileProvider(): array
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
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has('someDir')->willReturn(false);

        $loader = new FileSystemLoader($fileSystem->reveal(), '');
        $loader->getSourceContext('someDir');
    }

    public function testFileDoesNotExist(): void
    {
        $this->expectException(LoaderError::class);
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has('someDir')->willReturn(false);

        $loader = new FileSystemLoader($fileSystem->reveal(), '');
        $loader->getSourceContext('someDir');
    }

    public function testLoadFileWithPrefix(): void
    {
        $fileSystem = $this->prophesize(FileSystem::class);
        $fileSystem->has('test/file.twig')->willReturn(true);
        $fileSystem->read('test/file.twig')->willReturn('content');

        $loader = new FileSystemLoader($fileSystem->reveal(), 'test/');
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
