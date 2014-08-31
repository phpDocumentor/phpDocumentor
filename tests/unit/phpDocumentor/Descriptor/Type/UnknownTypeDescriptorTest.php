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

class UnknownTypeDescriptorTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Descriptor\Type\UnknownTypeDescriptor::getName
     * @covers phpDocumentor\Descriptor\Type\UnknownTypeDescriptor::__toString
     */
    public function testIfNameCanBeReturned()
    {
        $fixture = new UnknownTypeDescriptor('unknowntype');

        $this->assertSame('unknowntype', $fixture->getName());
        $this->assertSame('unknowntype', (string) $fixture);
    }
}
