<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the LinkDescriptor class.
 */
class LinkDescriptorTest extends \PHPUnit_Framework_TestCase {

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

        $expected = 'link';
        $this->fixture->setLink($expected);
        $this->assertEquals($expected, $this->fixture->getLink());
    }

} 