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

namespace phpDocumentor\Plugin;

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
     * @covers phpDocumentor\Plugin\Parameter::getKey
     */
    public function testSetAndGetKey()
    {
        $this->assertNull($this->fixture->getKey());
    }

    /**
     * @covers phpDocumentor\Plugin\Parameter::getValue
     */
    public function testSetAndGetValue()
    {
        $this->assertNull($this->fixture->getValue());
    }
}
