<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

use Mockery as m;
use phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

/**
 * Test class for the BaseEntry object.
 */
class BaseEntryTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /**
     * @var BaseEntry
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {
        $this->object = m::mock('phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry[]');
    }

    /**
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::getParent
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::setParent
     */
    public function testAddingAParentEntry()
    {
        $this->assertNull($this->object->getParent());

        $heading = new Heading();
        $this->object->setParent($heading);

        $this->assertSame($heading, $this->object->getParent());
    }

    /**
     * @expectedException \InvalidArgumentException
     *
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::setParent
     */
    public function testAddingAnInvalidParentEntry()
    {
        $toc = new TableOfContents();
        $this->object->setParent($toc);
    }

    /**
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::getChildren
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::addChild
     */
    public function testAddingChildren()
    {
        $this->assertEmpty($this->object->getChildren());

        $heading = new Heading();
        $this->object->addChild($heading);

        $this->assertCount(1, $this->object->getChildren());
        $this->assertSame([$heading], $this->object->getChildren());
    }

    /**
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::getName
     * @covers phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents\BaseEntry::setName
     */
    public function testSettingAName()
    {
        $this->assertSame('', $this->object->getName());

        $this->object->setName('name');

        $this->assertSame('name', $this->object->getName());
    }
}
