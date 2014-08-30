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

class IntegerDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Descriptor\Type\IntegerDescriptor::getName
     * @covers phpDocumentor\Descriptor\Type\IntegerDescriptor::__toString
     */
    public function testIfNameCanBeReturned()
    {
        $fixture = new IntegerDescriptor();

        $this->assertSame('integer', $fixture->getName());
        $this->assertSame('integer', (string) $fixture);
    }
}
