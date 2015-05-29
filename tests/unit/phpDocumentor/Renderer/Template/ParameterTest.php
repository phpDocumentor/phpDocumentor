<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Renderer\Template;

/**
 * Tests the functionality for the Parameter class.
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetKey()
    {
        $fixture = new Parameter('key', 'value');
        $this->assertSame('key', $fixture->getKey());
    }

    public function testSetAndGetValue()
    {
        $fixture = new Parameter('key', 'value');
        $this->assertSame('value', $fixture->getValue());
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The key for a parameter is supposed to be a string, received true
     */
    public function testErrorIsThrownIfKeyIsNotAString()
    {
        new Parameter(true, 'value');
    }

    /**
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage The value for a parameter is supposed to be a string, received true
     */
    public function testErrorIsThrownIfValueIsNotAString()
    {
        new Parameter('key', true);
    }
}
