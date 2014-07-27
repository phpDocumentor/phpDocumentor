<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the LinkDescriptor class.
 */
class LinkDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_LINK = 'http://phpdoc.org';

    /** @var LinkDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new LinkDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\LinkDescriptor::setLink
     * @covers phpDocumentor\Descriptor\Tag\LinkDescriptor::getLink
     */
    public function testSetAndGetLink()
    {
        $this->assertEmpty($this->fixture->getLink());

        $this->fixture->setLink(self::EXAMPLE_LINK);
        $result = $this->fixture->getLink();

        $this->assertSame(self::EXAMPLE_LINK, $result);
    }
}
