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

use \Mockery as m;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the ProjectDescriptor class.
 */
class ProjectDescriptorTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setName
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getName
     */
    public function testGetSetName()
    {
        $projectName = 'Initial name';
        $descriptor = new ProjectDescriptor($projectName);
        $result = $descriptor->getName();
        $this->assertEquals($projectName, $result);

        $projectName = 'Renamed';
        $descriptor->setName($projectName);
        $result = $descriptor->getName();
        $this->assertEquals($projectName, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setFiles
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     */
    public function testGetSetFiles()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $result = $descriptor->getFiles();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $filesCollection = new Collection();
        $descriptor->setFiles($filesCollection);

        $result = $descriptor->getFiles();
        $this->assertSame($filesCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setIndexes
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getIndexes
     */
    public function testGetSetIndexes()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $result = $descriptor->getIndexes();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $indexCollection = new Collection();
        $descriptor->setIndexes($indexCollection);
        $result = $descriptor->getIndexes();
        $this->assertSame($indexCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setNamespace
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getNamespace
     */
    public function testGetSetNamespace()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $result = $descriptor->getNamespace();
        $this->assertInstanceOf('phpDocumentor\Descriptor\NamespaceDescriptor', $result);
        $namespaceDescriptor = new NamespaceDescriptor();
        $descriptor->setNamespace($namespaceDescriptor);
        $result = $descriptor->getNamespace();
        $this->assertSame($namespaceDescriptor, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setSettings
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getSettings
     */
    public function testGetSetSettings()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $result = $descriptor->getSettings();
        $this->assertInstanceOf('phpDocumentor\Descriptor\ProjectDescriptor\Settings', $result);
        $settings = new Settings();
        $descriptor->setSettings($settings);
        $result = $descriptor->getSettings();
        $this->assertSame($settings, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setPartials
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getPartials
     */
    public function testGetSetPartials()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $result = $descriptor->getPartials();
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $result);

        $partialsCollection = new Collection();
        $descriptor->setPartials($partialsCollection);

        $result = $descriptor->getPartials();
        $this->assertSame($partialsCollection, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::isVisibilityAllowed
     */
    public function testIsVisibilityAllowed()
    {
        $descriptor = new ProjectDescriptor('Project name');
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));

        $Settings = new Settings();
        $Settings->setVisibility(Settings::VISIBILITY_PROTECTED);
        $descriptor->setSettings($Settings);

        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));

        $Settings->setVisibility(Settings::VISIBILITY_PROTECTED | Settings::VISIBILITY_INTERNAL);
        $descriptor->setSettings($Settings);

        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PROTECTED));
        $this->assertFalse($descriptor->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
        $this->assertTrue($descriptor->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));
    }
}
