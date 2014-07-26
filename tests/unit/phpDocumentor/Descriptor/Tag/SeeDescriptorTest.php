<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the SeeDescriptor class.
 */
class SeeDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var SeeDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new SeeDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\SeeDescriptor::setReference
     * @covers phpDocumentor\Descriptor\Tag\SeeDescriptor::getReference
     */
    public function testSetAndGetReference()
    {
        $this->assertEmpty($this->fixture->getReference());

        $expected = 'reference';
        $this->fixture->setReference($expected);
        $this->assertEquals($expected, $this->fixture->getReference());
    }
} 