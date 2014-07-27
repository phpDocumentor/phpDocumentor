<?php

namespace phpDocumentor\Descriptor\Tag;


use phpDocumentor\Descriptor\Collection;

/**
 * Tests the functionality for the ReturnDescriptor class.
 */
class ReturnDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var ReturnDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new ReturnDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::setTypes
     * @covers phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::getTypes
     */
    public function testSetAndGetTypes()
    {
        $this->assertEmpty($this->fixture->getTypes());

        $expected = new Collection(array('a' => 'b'));
        $this->fixture->setTypes($expected);
        $this->assertEquals($expected, $this->fixture->getTypes());

    }
} 