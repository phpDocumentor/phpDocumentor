<?php

namespace phpDocumentor\Descriptor\Tag;

/**
 * Tests the functionality for the PropertyDescriptor class.
 */
class PropertyDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var PropertyDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new PropertyDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract::setVariableName
     * @covers phpDocumentor\Descriptor\Tag\BaseTypes\TypedVariableAbstract::getVariableName
     */
    public function testSetAndGetVariableName()
    {
        $this->assertEmpty($this->fixture->getVariableName());

        $expected = 'variableName';
        $this->fixture->setVariableName($expected);
        $this->assertEquals($expected, $this->fixture->getVariableName());

    }
} 