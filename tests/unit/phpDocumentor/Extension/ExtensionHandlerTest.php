<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use org\bovigo\vfs\vfsStream;
use PHPUnit\Framework\TestCase;
use ReflectionClass;

use function iterator_to_array;

/**
 * @coversDefaultClass \phpDocumentor\Extension\ExtensionHandler
 * @covers ::<private>
 * @covers ::__construct
 * @covers ::getInstance
 */
final class ExtensionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        $class = new ReflectionClass(ExtensionHandler::getInstance(
            [
                'foo',
                'bar',
            ],
        ));

        $instance = $class->getProperty('instance');
        $instance->setAccessible(true);
        $instance->setValue(null, null);
        $instance->setAccessible(false);
    }

    /** @covers ::loadExtensions */
    public function testLoadExtensionsFromEmptyDirResultsInEmptyExtensions(): void
    {
        $root = vfsStream::setup();
        $root->addChild(vfsStream::newDirectory('extensions'));

        $extensionsDir = $root->url() . '/extensions';

        $extensionHandler = ExtensionHandler::getInstance([$extensionsDir]);

        $manifests = iterator_to_array($extensionHandler->loadExtensions());
        self::assertCount(0, $manifests);
    }

    /** @covers ::loadExtensions */
    public function testLoadExtensionsFromNonExistingDirResultsInEmptyArray(): void
    {
        $root = vfsStream::setup();
        $root->addChild(vfsStream::newDirectory('extensions'));

        $extensionsDir = $root->url() . '/extensions';

        $extensionHandler = ExtensionHandler::getInstance([$extensionsDir . '/notExisting']);
        $manifests = iterator_to_array($extensionHandler->loadExtensions());
        self::assertCount(0, $manifests);
    }

    /** @covers ::loadExtensions */
    public function testLoadExtensionsIgnoresNonPharFiles(): void
    {
        $root = vfsStream::setup();
        $extensionsDir = vfsStream::newDirectory('extensions');
        $extensionsDir->addChild(vfsStream::newFile('somefile.txt')->withContent('ignore'));
        $root->addChild($extensionsDir);

        $extensionHandler = ExtensionHandler::getInstance([$extensionsDir->url()]);
        $manifests = iterator_to_array($extensionHandler->loadExtensions());
        self::assertCount(0, $manifests);
    }

    /** @covers ::loadExtensions */
    public function testLoadExtensionFromDir(): void
    {
        $root = vfsStream::setup();
        $extensionsDir = vfsStream::newDirectory('extensions');
        vfsStream::copyFromFileSystem(__DIR__ . '/../../../data/extensions', $extensionsDir);
        $root->addChild($extensionsDir);

        $extensionHandler = ExtensionHandler::getInstance([$extensionsDir->url()]);
        $manifests = iterator_to_array($extensionHandler->loadExtensions());
        self::assertCount(1, $manifests);
    }
}
