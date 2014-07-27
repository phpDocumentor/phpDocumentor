<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the DeprectatedDescriptor class.
 */
class DeprecatedDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /** @var DeprecatedDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new DeprecatedDescriptor('name');
    }


    /**
     * @covers phpDocumentor\Descriptor\Tag\DeprecatedDescriptor::setVersion
     * @covers phpDocumentor\Descriptor\Tag\DeprecatedDescriptor::getVersion
     */
    public function testSetAndGetVersion()
    {
        $this->assertEmpty($this->fixture->getVersion());

        $expected = 'version';
        $this->fixture->setVersion($expected);
        $this->assertEquals($expected, $this->fixture->getVersion());

    }
}
