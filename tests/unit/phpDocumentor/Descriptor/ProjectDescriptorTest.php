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
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the ProjectDescriptor class.
 */
class ProjectDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var ProjectDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ProjectDescriptor('project');
    }


    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setName
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getName
     */
    public function testSettingAndGettingProjectName()
    {
        $this->assertEquals('project', $this->fixture->getName());

        $this->fixture->setName('projectModified');

        $this->assertEquals('projectModified', $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setNamespace
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getNamespace
     */
    public function testSettingAndGettingProjectNamespace()
    {
        $expected = new NamespaceDescriptor();
        $expected->setName('\\');
        $expected->setFullyQualifiedStructuralElementName('\\');

        $this->assertEquals($expected, $this->fixture->getNamespace());

        $expected->setName('\\someNamespace\\subNamespace\\');
        $expected->setFullyQualifiedStructuralElementName('\\someNamespace\\subNamespace\\');

        $this->fixture->setNamespace($expected);

        $this->assertSame($expected, $this->fixture->getNamespace());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setSettings
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getSettings
     */
    public function testSettingAndGettingProjectSettings()
    {
        $this->assertEquals(new Settings(), $this->fixture->getSettings());

        $expected = new Settings();

        $this->fixture->setSettings($expected);

        $this->assertSame($expected, $this->fixture->getSettings());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::isVisibilityAllowed
     */
    public function testSettingAndGettingWhetherProjectIsVisibilityAllowed()
    {
        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_DEFAULT));

        $this->fixture->getSettings()->setVisibility(Settings::VISIBILITY_INTERNAL);

        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_INTERNAL));
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::setIndexes
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getIndexes
     */
    public function testSettingAndGettingProjectIndexes()
    {
        $this->assertEquals(new Collection(), $this->fixture->getIndexes());

        $expected = new Collection(array(1));
        $this->fixture->setIndexes($expected);

        $this->assertSame($expected, $this->fixture->getIndexes());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     */
    public function testSettingAndGettingProjectFilesCollection()
    {
        $this->assertEquals(new Collection(), $this->fixture->getFiles());

        $expected = new Collection(array(1));
        $this->fixture->setFiles($expected);

        $this->assertSame($expected, $this->fixture->getFiles());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     */
    public function testSettingAndGettingProjectFilesFileDescriptors()
    {
        $this->assertEquals(new Collection(), $this->fixture->getFiles());

        $expected = new Collection(array(new FileDescriptor('hash1'), new FileDescriptor('hash2')));
        $this->fixture->setFiles($expected);

        $this->assertSame($expected, $this->fixture->getFiles());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     * @covers phpDocumentor\Descriptor\ProjectDescriptor::getFiles
     */
    public function testSettingAndGettingProjectPartials()
    {
        $this->assertEquals(new Collection(), $this->fixture->getPartials());

        $expected = new Collection(array(1));
        $this->fixture->setPartials($expected);

        $this->assertSame($expected, $this->fixture->getPartials());
    }

}
