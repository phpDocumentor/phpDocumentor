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

use phpDocumentor\Reflection\Types\Array_;

/**
 * Tests the functionality for the ReturnDescriptor class.
 */
class ReturnDescriptorTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
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
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::setTypes
     * @covers \phpDocumentor\Descriptor\Tag\BaseTypes\TypedAbstract::getTypes
     */
    public function testSetAndGetTypes()
    {
        $expected = new Array_();
        $this->assertEmpty($this->fixture->getTypes());

        $this->fixture->setTypes($expected);
        $result = $this->fixture->getTypes();

        $this->assertEquals($expected, $result);

    }
}
