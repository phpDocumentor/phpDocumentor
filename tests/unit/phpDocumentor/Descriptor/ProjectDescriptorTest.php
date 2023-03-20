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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Faker\Faker;
use PHPUnit\Framework\TestCase;

/**
 * Tests the functionality for the ProjectDescriptor class.
 *
 * @coversDefaultClass \phpDocumentor\Descriptor\ProjectDescriptor
 * @covers ::__construct
 * @covers ::<private>
 */
final class ProjectDescriptorTest extends TestCase
{
    use Faker;

    public const EXAMPLE_NAME = 'Initial name';

    private ProjectDescriptor $fixture;

    /**
     * Initializes the fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new ProjectDescriptor(self::EXAMPLE_NAME);
    }

    /**
     * @covers ::setName
     * @covers ::getName
     */
    public function testGetSetName(): void
    {
        $this->assertEquals(self::EXAMPLE_NAME, $this->fixture->getName());

        $newProjectName = 'Renamed';
        $this->fixture->setName($newProjectName);

        $this->assertEquals($newProjectName, $this->fixture->getName());
    }

    /**
     * @covers ::getFiles
     */
    public function testGetFilesFromApiSet(): void
    {
        $set = $this->faker()->apiSetDescriptor();
        $version = $this->faker()->versionDescriptor([$set]);
        $this->fixture->getVersions()->add($version);

        $this->assertSame($set->getFiles(), $this->fixture->getFiles());
    }

    /**
     * @covers ::getIndexes
     */
    public function testGetIndexesFromApiSet(): void
    {
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $this->fixture->getVersions()->add($this->faker()->versionDescriptor([$apiSetDescriptor]));

        $this->assertSame($apiSetDescriptor->getIndexes(), $this->fixture->getIndexes());
    }

    /**
     * @covers ::setNamespace
     * @covers ::getNamespace
     */
    public function testGetSetNamespace(): void
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
    public function testRootPackageIsSetForProject(): void
    {
        $this->assertInstanceOf(PackageDescriptor::class, $this->fixture->getPackage());
        $this->assertSame('\\', (string) $this->fixture->getPackage()->getName());
        $this->assertSame('\\', (string) $this->fixture->getPackage()->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::setSettings
     * @covers ::getSettings
     */
    public function testGetSetSettings(): void
    {
        $this->assertInstanceOf(Settings::class, $this->fixture->getSettings());

        $settings = new Settings();
        $this->fixture->setSettings($settings);

        $this->assertSame($settings, $this->fixture->getSettings());
    }

    /**
     * @covers ::setPartials
     * @covers ::getPartials
     */
    public function testGetSetPartials(): void
    {
        $result = $this->fixture->getPartials();
        $this->assertInstanceOf(Collection::class, $result);

        $partialsCollection = new Collection();
        $this->fixture->setPartials($partialsCollection);

        $result = $this->fixture->getPartials();
        $this->assertSame($partialsCollection, $result);
    }
}
