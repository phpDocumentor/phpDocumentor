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

use Mockery as m;

/**
 * Tests the functionality for the DescriptorAbstract class.
 */
class DescriptorAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var DescriptorAbstract $fixture */
    protected $fixture;

    /**
     * Creates a new mocked fixture object.
     */
    protected function setUp()
    {
        $this->fixture = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $this->fixture->shouldDeferMissing();
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::__construct
     */
    public function testInitialize()
    {
        $mock = $this->getMockBuilder('phpDocumentor\Descriptor\DescriptorAbstract')
            ->disableOriginalConstructor()
            ->getMock();
        $mock->expects($this->once())->method('setTags')->with(new Collection());
        $mock->expects($this->once())->method('setErrors')->with(new Collection());
        $mock->__construct();
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setFullyQualifiedStructuralElementName
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getFullyQualifiedStructuralElementName
     */
    public function testSettingAndGettingFullyQualifiedStructuralElementName()
    {
        $this->assertSame('', $this->fixture->getFullyQualifiedStructuralElementName());

        $this->fixture->setFullyQualifiedStructuralElementName('elementname');

        $this->assertSame('elementname', $this->fixture->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setName
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getName
     */
    public function testSettingAndGettingName()
    {
        $this->assertSame('', $this->fixture->getName());

        $this->fixture->setName('name');

        $this->assertSame('name', $this->fixture->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setNamespace
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getNamespace
     */
    public function testSettingAndGettingNamespace()
    {
        $this->assertNull($this->fixture->getNamespace());

        $mock = m::mock('phpDocumentor\Descriptor\NamespaceDescriptor');

        $this->fixture->setNamespace($mock);

        $this->assertSame($mock, $this->fixture->getNamespace());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setSummary
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getSummary
     */
    public function testSettingAndGettingSummary()
    {
        $this->assertSame('', $this->fixture->getSummary());

        $this->fixture->setSummary('summary');

        $this->assertSame('summary', $this->fixture->getSummary());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setDescription
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getDescription
     */
    public function testSettingAndGettingDescription()
    {
        $this->assertSame('', $this->fixture->getDescription());

        $this->fixture->setDescription('description');

        $this->assertSame('description', $this->fixture->getDescription());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setPackage
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getPackage
     */
    public function testSettingAndGettingPackage()
    {
        $package = new PackageDescriptor();
        $this->assertSame(null, $this->fixture->getPackage());

        $this->fixture->setPackage($package);

        $this->assertSame($package, $this->fixture->getPackage());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getAuthor
     */
    public function testGetAuthor()
    {
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $author = new Collection(array('author'));

        $collection = new Collection();
        $collection->offsetSet('author', $author);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($author, $mock->getAuthor());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getVersion
     */
    public function testGetVersion()
    {
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $version = new Collection(array('version'));

        $collection = new Collection();
        $collection->offsetSet('version', $version);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($version, $mock->getVersion());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getCopyright
     */
    public function testGetCopyRight()
    {
        $mock = m::mock(
            'phpDocumentor\Descriptor\DescriptorAbstract, phpDocumentor\Descriptor\Interfaces\ChildInterface'
        );
        $mock->shouldDeferMissing();

        $copyright = new Collection(array('copyright'));

        $collection = new Collection();
        $collection->offsetSet('copyright', $copyright);

        $mock->shouldReceive('getTags')->andReturn($collection);
        $this->assertSame($copyright, $mock->getCopyright());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setLocation
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getFile
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getLine
     */
    public function testSettingAndGettingLocation()
    {
        $this->assertNull($this->fixture->getFile());
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLocation(m::mock('phpDocumentor\Descriptor\FileDescriptor'), 5);

        $this->assertInstanceOf('phpDocumentor\Descriptor\FileDescriptor', $this->fixture->getFile());
        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setLine
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getLine
     */
    public function testSetLineNumber()
    {
        $this->assertSame(0, $this->fixture->getLine());

        $this->fixture->setLine(5);

        $this->assertSame(5, $this->fixture->getLine());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getPath
     */
    public function testGetPath()
    {
        $this->assertSame('', $this->fixture->getPath());

        /** @var FileDescriptor $file */
        $file = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $file->shouldReceive('getPath')->andReturn('path');
        $this->fixture->setLocation($file);

        $this->assertSame('path', $this->fixture->getPath());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setTags
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getTags
     */
    public function testSettingAndGettingTags()
    {
        $this->assertNull($this->fixture->getTags());

        /** @var Collection $mock */
        $mock = m::mock('phpDocumentor\Descriptor\Collection');
        $this->fixture->setTags($mock);

        $this->assertSame($mock, $this->fixture->getTags());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::isDeprecated
     */
    public function testIsDeprecated()
    {
        $this->assertFalse($this->fixture->isDeprecated());

        $this->fixture->setTags(new Collection(array('deprecated' => 'deprecated')));

        $this->assertTrue($this->fixture->isDeprecated());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::setErrors
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::getErrors
     */
    public function testSettingAndGettingErrors()
    {
        $this->assertNull($this->fixture->getErrors());

        /** @var Collection $mock */
        $mock = m::mock('phpDocumentor\Descriptor\Collection');
        $this->fixture->setErrors($mock);

        $this->assertSame($mock, $this->fixture->getErrors());
    }

    /**
     * @covers phpDocumentor\Descriptor\DescriptorAbstract::__toString
     */
    public function testToString()
    {
        $this->fixture->setFullyQualifiedStructuralElementName('fqn');
        $this->assertSame('fqn', (string) $this->fixture);
    }

}
