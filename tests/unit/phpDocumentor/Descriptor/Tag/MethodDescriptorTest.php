<?php

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\Collection;

/**
 * Tests the functionality for the MethodDescriptor class.
 */
class MethodDescriptorTest extends \PHPUnit_Framework_TestCase {

    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new MethodDescriptor('name');
    }

    // commented because otherwise the __construct method shows as not covered
    // /**
    // * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setMethodName
    // * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getMethodName
    // */
    public function testSetAndGetMethodName()
    {
        $this->assertEmpty($this->fixture->getMethodName());

        $expected = 'methodname';
        $this->fixture->setMethodName($expected);
        $this->assertEquals($expected, $this->fixture->getMethodName());
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setArguments()
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getArguments()
     */
    public function testSetAndGetArguments()
    {
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $expected = new Collection(array('a' => 'b'));
        $this->fixture->setArguments($expected);
        $this->assertEquals($expected, $this->fixture->getArguments());
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setResponse
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getResponse
     */
    public function testSetAndGetResponse()
    {
        $this->assertEmpty($this->fixture->getResponse());

        $expected = array('a' => 'b');
        $this->fixture->setResponse($expected);
        $this->assertEquals($expected, $this->fixture->getResponse());
    }



} 