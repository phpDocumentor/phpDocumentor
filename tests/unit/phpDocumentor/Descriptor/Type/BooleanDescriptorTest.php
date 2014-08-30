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

class BooleanDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Descriptor\Type\BooleanDescriptor::getName
     * @covers phpDocumentor\Descriptor\Type\BooleanDescriptor::__toString
     */
    public function testIfNameCanBeReturned()
    {
        $fixture = new BooleanDescriptor();

        $this->assertSame('boolean', $fixture->getName());
        $this->assertSame('boolean', (string) $fixture);
    }
}
