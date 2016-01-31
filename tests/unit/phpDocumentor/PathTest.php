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

namespace phpDocumentor;

use phpDocumentor\DomainModel\Path;

/**
 * Test case for Path
 * @coversDefaultClass phpDocumentor\Path
 */
class PathTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::__toString
     */
    public function testToString()
    {
        $path = new Path('/my/Path');

        $this->assertEquals('/my/Path', (string)$path);
    }
}
