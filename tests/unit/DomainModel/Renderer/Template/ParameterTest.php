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

namespace phpDocumentor\DomainModel\Renderer\Template;

/**
 * @coversDefaultClass phpDocumentor\Application\Renderer\Template\Parameter
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getKey
     */
    public function testSetAndGetKey()
    {
        $fixture = new Parameter('key', 'value');
        $this->assertSame('key', $fixture->getKey());
    }

    /**
     * @covers ::__construct
     * @covers ::getValue
     */
    public function testSetAndGetValue()
    {
        $fixture = new Parameter('key', 'value');
        $this->assertSame('value', $fixture->getValue());
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage The key for a parameter is supposed to be a string, received true
     * @covers ::__construct
     */
    public function testErrorIsThrownIfKeyIsNotAString()
    {
        new Parameter(true, 'value');
    }
}
