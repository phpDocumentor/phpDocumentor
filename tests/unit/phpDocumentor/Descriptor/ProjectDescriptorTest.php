<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the ProjectDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\ProjectDescriptor
 */
final class ProjectDescriptorTest extends MockeryTestCase
{
    public const EXAMPLE_NAME = 'Initial name';

    /** @var ProjectDescriptor */
    private $fixture;

    /**
     * Initializes the fixture object.
     */
    protected function setUp() : void
    {
        $this->fixture = new ProjectDescriptor(self::EXAMPLE_NAME);
    }

    /**
     * @covers ::__construct
     * @covers ::setName
     * @covers ::getName
     */
    public function testGetSetName() : void
    {
        $this->assertEquals(self::EXAMPLE_NAME, $this->fixture->getName());

        $newProjectName = 'Renamed';
        $this->fixture->setName($newProjectName);

        $this->assertEquals($newProjectName, $this->fixture->getName());
    }

    /**
     * @covers ::__construct
     * @covers ::setFiles
     * @covers ::getFiles
     */
    public function testGetSetFiles() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getFiles());

        $filesCollection = new Collection();
        $this->fixture->setFiles($filesCollection);

        $this->assertSame($filesCollection, $this->fixture->getFiles());
    }

    /**
     * @covers ::__construct
     * @covers ::setIndexes
     * @covers ::getIndexes
     */
    public function testGetSetIndexes() : void
    {
        $this->assertInstanceOf(Collection::class, $this->fixture->getIndexes());

        $indexCollection = new Collection();
        $this->fixture->setIndexes($indexCollection);

        $this->assertSame($indexCollection, $this->fixture->getIndexes());
    }

    /**
     * @covers ::setNamespace
     * @covers ::getNamespace
     */
    public function testGetSetNamespace() : void
    {
        $this->assertInstanceOf(NamespaceDescriptor::class, $this->fixture->getNamespace());

        $namespaceDescriptor = new NamespaceDescriptor();
        $this->fixture->setNamespace($namespaceDescriptor);

        $this->assertSame($namespaceDescriptor, $this->fixture->getNamespace());
    }

    /**
     * @covers ::setNamespace
     * @covers ::getNamespace
     */
    public function testRootPackageIsSetForProject() : void
    {
        $this->assertInstanceOf(PackageDescriptor::class, $this->fixture->getPackage());
        $this->assertSame('\\', (string) $this->fixture->getPackage()->getName());
        $this->assertSame('\\', (string) $this->fixture->getPackage()->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::setSettings
     * @covers ::getSettings
     */
    public function testGetSetSettings() : void
    {
        $this->assertInstanceOf(Settings::class, $this->fixture->getSettings());

        $settings = new Settings();
        $this->fixture->setSettings($settings);

        $this->assertSame($settings, $this->fixture->getSettings());
    }

    /**
     * @covers ::__construct
     * @covers ::setPartials
     * @covers ::getPartials
     */
    public function testGetSetPartials() : void
    {
        $result = $this->fixture->getPartials();
        $this->assertInstanceOf(Collection::class, $result);

        $partialsCollection = new Collection();
        $this->fixture->setPartials($partialsCollection);

        $result = $this->fixture->getPartials();
        $this->assertSame($partialsCollection, $result);
    }

    /**
     * @covers ::isVisibilityAllowed
     */
    public function testIsVisibilityAllowed() : void
    {
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));

        $settings = new Settings();
        $settings->setVisibility(Settings::VISIBILITY_PROTECTED);
        $this->fixture->setSettings($settings);

        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));

        $settings->setVisibility(Settings::VISIBILITY_PROTECTED | Settings::VISIBILITY_INTERNAL);
        $this->fixture->setSettings($settings);

        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));
    }
}
