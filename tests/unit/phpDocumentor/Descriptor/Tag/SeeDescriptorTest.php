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

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the SeeDescriptor class.
 */
class SeeDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    const EXAMPLE_REFERENCE = 'reference';

    /** @var SeeDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp(): void
    {
        $this->fixture = new SeeDescriptor('name');
    }

    /**
     * @covers \phpDocumentor\Descriptor\Tag\SeeDescriptor::setReference
     * @covers \phpDocumentor\Descriptor\Tag\SeeDescriptor::getReference
     */
    public function testSetAndGetReference() : void
    {
        $this->assertEmpty($this->fixture->getReference());

        $this->fixture->setReference(self::EXAMPLE_REFERENCE);
        $result = $this->fixture->getReference();

        $this->assertEquals(self::EXAMPLE_REFERENCE, $result);
    }
}
