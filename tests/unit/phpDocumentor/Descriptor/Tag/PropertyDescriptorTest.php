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

/**
 * Tests the functionality for the PropertyDescriptor class.
 */
class PropertyDescriptorTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_NAME = 'variableName';

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

        $this->fixture->setVariableName(self::EXAMPLE_NAME);
        $result = $this->fixture->getVariableName();

        $this->assertSame(self::EXAMPLE_NAME, $result);
    }
}
