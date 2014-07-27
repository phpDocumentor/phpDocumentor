<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the SinceDescriptor class.
 */
class SinceDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var SinceDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new SinceDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\SinceDescriptor::setVersion
     * @covers phpDocumentor\Descriptor\Tag\SinceDescriptor::getVersion
     */
    public function testSetAndGetVersion()
    {
        $this->assertEmpty($this->fixture->getVersion());

        $expected = 'version';
        $this->fixture->setVersion($expected);
        $this->assertEquals($expected, $this->fixture->getVersion());
    }
} 