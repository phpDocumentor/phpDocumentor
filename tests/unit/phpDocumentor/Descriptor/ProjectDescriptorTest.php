<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the ProjectDescriptor class.
 */
class ProjectDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_NAME = 'Initial name';

    /** @var ProjectDescriptor */
    private $fixture;

    /**
     * Initializes the fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ProjectDescriptor(self::EXAMPLE_NAME);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setName
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getName
     */
    public function testGetSetName()
    {
        $this->assertEquals(self::EXAMPLE_NAME, $this->fixture->getName());

        $newProjectName = 'Renamed';
        $this->fixture->setName($newProjectName);

        $this->assertEquals($newProjectName, $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setFiles
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     */
    public function testGetSetFiles()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getFiles());

        $filesCollection = new Collection();
        $this->fixture->setFiles($filesCollection);

        $this->assertSame($filesCollection, $this->fixture->getFiles());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setIndexes
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getIndexes
     */
    public function testGetSetIndexes()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getIndexes());

        $indexCollection = new Collection();
        $this->fixture->setIndexes($indexCollection);

        $this->assertSame($indexCollection, $this->fixture->getIndexes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setNamespace
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getNamespace
     */
    public function testGetSetNamespace()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\NamespaceDescriptor', $this->fixture->getNamespace());

        $namespaceDescriptor = new NamespaceDescriptor();
        $this->fixture->setNamespace($namespaceDescriptor);

        $this->assertSame($namespaceDescriptor, $this->fixture->getNamespace());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setSettings
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getSettings
     */
    public function testGetSetSettings()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\ProjectDescriptor\Settings', $this->fixture->getSettings());

        $settings = new Settings();
        $this->fixture->setSettings($settings);

        $this->assertSame($settings, $this->fixture->getSettings());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::__construct
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setPartials
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getPartials
     */
    public function testGetSetPartials()
    {
        $result = $this->fixture->getPartials();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $partialsCollection = new Collection();
        $this->fixture->setPartials($partialsCollection);

        $result = $this->fixture->getPartials();
        $this->assertSame($partialsCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::isVisibilityAllowed
     */
    public function testIsVisibilityAllowed()
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
