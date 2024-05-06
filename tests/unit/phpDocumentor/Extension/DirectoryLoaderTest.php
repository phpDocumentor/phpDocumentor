<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use DirectoryIterator;
use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Extension\DirectoryLoader */
final class DirectoryLoaderTest extends TestCase
{
    public function testSupportsReturnsFalseWhenNoManifestFileFoundInEmptyDir(): void
    {
        $fileSystem = vfsStream::setup('extensions');
        $fileSystem->addChild(vfsStream::newDirectory('myExtension'));

        $loader = new DirectoryLoader();
        self::assertFalse($loader->supports(new DirectoryIterator($fileSystem->url())));
    }

    public function testSupportsReturnsFalseWhenNoManifestFileFound(): void
    {
        $fileSystem = vfsStream::setup('extensions');
        $fileSystem->addChild(vfsStream::newFile('myExtension'));

        $loader = new DirectoryLoader();
        self::assertFalse($loader->supports(new DirectoryIterator($fileSystem->url())));
    }

    public function testSupportsReturnsTrueWhenManifestFileFound(): void
    {
        $fileSystem = vfsStream::setup('extensions');
        $fileSystem->addChild(vfsStream::newFile('manifest.xml'));

        $loader = new DirectoryLoader();
        self::assertTrue($loader->supports(new DirectoryIterator($fileSystem->url())));
    }

    public function testNotSupportedDirectoryReturnsNullOnLoad(): void
    {
        $fileSystem = vfsStream::setup('extensions');
        $fileSystem->addChild(vfsStream::newFile('myExtension'));

        $loader = new DirectoryLoader();
        self::assertNull($loader->load(new DirectoryIterator($fileSystem->url())));
    }

    public function testExtensionIsCreatedWhenExtensionDirValid(): void
    {
        $fileSystem = vfsStream::copyFromFileSystem(__DIR__ . '/../../../data/extensions/example');

        $loader = new DirectoryLoader();
        $extension = $loader->load(new DirectoryIterator($fileSystem->url()));

        self::assertInstanceOf(ExtensionInfo::class, $extension);
    }
}
