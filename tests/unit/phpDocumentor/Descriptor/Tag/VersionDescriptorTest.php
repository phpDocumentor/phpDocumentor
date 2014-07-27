<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the VersionDescriptor class.
 */
class VersionDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var VersionDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new VersionDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\VersionDescriptor::setVersion
     * @covers phpDocumentor\Descriptor\Tag\VersionDescriptor::getVersion
     */
    public function testSetAndGetVersion()
    {
        $this->assertEmpty($this->fixture->getVersion());

        $expected = 'version';
        $this->fixture->setVersion($expected);
        $this->assertEquals($expected, $this->fixture->getVersion());
    }

}
