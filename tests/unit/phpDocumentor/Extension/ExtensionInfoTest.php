<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ApplicationName;
use PharIo\Manifest\AuthorCollection;
use PharIo\Manifest\BundledComponent;
use PharIo\Manifest\BundledComponentCollection;
use PharIo\Manifest\CopyrightInformation;
use PharIo\Manifest\Extension;
use PharIo\Manifest\License;
use PharIo\Manifest\Manifest;
use PharIo\Manifest\RequirementCollection;
use PharIo\Manifest\Url;
use PharIo\Version\AnyVersionConstraint;
use PharIo\Version\Version;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Extension\ExtensionInfo */
final class ExtensionInfoTest extends TestCase
{
    public function testExtensionIsCreatedCorrectlyFromManifest(): void
    {
        $components = new BundledComponentCollection();
        $components->add(
            new BundledComponent(
                'phpDocumentor\\Example\\ExampleExtension',
                new Version('1.0.0'),
            ),
        );

        $manifest = new Manifest(
            new ApplicationName('phpDocumentor/example'),
            new Version('1.0.0'),
            new Extension(
                new ApplicationName('phpDocumentor/phpDocumentor'),
                new AnyVersionConstraint(),
            ),
            new CopyrightInformation(
                new AuthorCollection(),
                new License('mit', new Url('https://phpdoc.org')),
            ),
            new RequirementCollection(),
            $components,
        );

        $extension = ExtensionInfo::fromManifest($manifest, 'dir');

        self::assertSame('phpDocumentor/example', $extension->getName());
        self::assertSame('1.0.0', $extension->getVersion());
        self::assertSame('phpDocumentor\\Example\\ExampleExtension', $extension->getExtensionClass());
        self::assertSame('phpDocumentor\\Example\\', $extension->getNamespace());
        self::assertSame($manifest, $extension->getManifest());
        self::assertSame('dir', $extension->getPath());
    }
}
