<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the UsesDescriptor class.
 */
class UsesDescriptorTest extends \PHPUnit_Framework_TestCase {

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

        $expected = 'reference';
        $this->fixture->setReference($expected);
        $this->assertEquals($expected, $this->fixture->getReference());
    }
} 