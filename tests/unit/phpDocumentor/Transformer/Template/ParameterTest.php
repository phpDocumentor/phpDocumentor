<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

/**
 * Tests the functionality for the Parameter class.
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /** @var Parameter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new Parameter();
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Parameter::getKey
     * @covers phpDocumentor\Transformer\Template\Parameter::setKey
     */
    public function testSetAndGetKey()
    {
        $this->assertSame(null, $this->fixture->getKey());

        $this->fixture->setKey('key');

        $this->assertSame('key', $this->fixture->getKey());
    }

    /**
     * @covers phpDocumentor\Transformer\Template\Parameter::getValue
     * @covers phpDocumentor\Transformer\Template\Parameter::setValue
     */
    public function testSetAndGetValue()
    {
        $this->assertSame(null, $this->fixture->getValue());

        $this->fixture->setValue('value');

        $this->assertSame('value', $this->fixture->getValue());
    }
}
