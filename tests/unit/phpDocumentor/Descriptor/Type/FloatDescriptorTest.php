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

namespace phpDocumentor\Descriptor\Type;

class FloatDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Descriptor\Type\FloatDescriptor::getName
     * @covers phpDocumentor\Descriptor\Type\FloatDescriptor::__toString
     */
    public function testIfNameCanBeReturned()
    {
        $fixture = new FloatDescriptor();

        $this->assertSame('float', $fixture->getName());
        $this->assertSame('float', (string) $fixture);
    }
}
