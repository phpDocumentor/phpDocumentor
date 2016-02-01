<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Version;

use phpDocumentor\DomainModel\Parser\Version\Definition;
use phpDocumentor\DomainModel\Parser\Version\Number;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Version\Definition
 */
class DefinitionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getVersionNumber
     */
    public function testVersionNumber()
    {
        $definition = new Definition(new Number('1.0.1'));

        $this->assertEquals(new Number('1.0.1'), $definition->getVersionNumber());
    }
}
