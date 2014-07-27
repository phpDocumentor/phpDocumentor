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
 * Tests the functionality for the UsesDescriptor class.
 */
class UsesDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_REFERENCE = 'reference';

    /** @var UsesDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new UsesDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\UsesDescriptor::setReference
     * @covers phpDocumentor\Descriptor\Tag\UsesDescriptor::getReference
     */
    public function testSetAndGetReference()
    {
        $this->assertEmpty($this->fixture->getReference());

        $this->fixture->setReference(self::EXAMPLE_REFERENCE);
        $result = $this->fixture->getReference();

        $this->assertEquals(self::EXAMPLE_REFERENCE, $result);
    }
}
