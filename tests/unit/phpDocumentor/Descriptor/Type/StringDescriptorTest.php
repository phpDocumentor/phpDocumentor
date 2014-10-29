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

class StringDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Descriptor\Type\StringDescriptor::getName
     * @covers phpDocumentor\Descriptor\Type\StringDescriptor::__toString
     */
    public function testIfNameCanBeReturned()
    {
        $fixture = new StringDescriptor();

        $this->assertSame('string', $fixture->getName());
        $this->assertSame('string', (string) $fixture);
    }
}
