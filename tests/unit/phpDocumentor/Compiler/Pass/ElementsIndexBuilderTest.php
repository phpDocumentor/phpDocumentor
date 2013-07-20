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

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;

/**
 * Tests the functionality for the ElementsIndexBuilder
 */
class ElementsIndexBuilderTest extends \PHPUnit_Framework_TestCase
{

    /**
     * @covers phpDocumentor\Compiler\Pass\ElementsIndexBuilder::getDescription
     */
    public function testGetDescription()
    {
        $builder = new ElementsIndexBuilder();
        $expected = 'Build "elements" index';
        $this->assertSame($expected, $builder->getDescription());
    }

 }
