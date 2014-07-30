<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\Collection;

/**
 * Tests the functionality for the MethodDescriptor class.
 */
class MethodDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_NAME = 'methodname';

    /** @var MethodDescriptor $fixture */
    protected $fixture;

    /**
     * Creates a new fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new MethodDescriptor('name');
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::__construct
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setMethodName
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getMethodName
     */
    public function testSetAndGetMethodName()
    {
        $this->assertEmpty($this->fixture->getMethodName());

        $this->fixture->setMethodName(self::EXAMPLE_NAME);
        $result = $this->fixture->getMethodName();

        $this->assertSame(self::EXAMPLE_NAME, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setArguments()
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getArguments()
     */
    public function testSetAndGetArguments()
    {
        $expected = new Collection(array('a' => 'b'));
        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $this->fixture->getArguments());

        $this->fixture->setArguments($expected);
        $result = $this->fixture->getArguments();

        $this->assertSame($expected, $result);
    }

    /**
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::setResponse
     * @covers phpDocumentor\Descriptor\Tag\MethodDescriptor::getResponse
     */
    public function testSetAndGetResponse()
    {
        $expected = array('a' => 'b');
        $this->assertEmpty($this->fixture->getResponse());

        $this->fixture->setResponse($expected);
        $result = $this->fixture->getResponse();

        $this->assertSame($expected, $result);
    }
}
