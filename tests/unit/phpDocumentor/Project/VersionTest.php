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

namespace phpDocumentor\Project;

use phpDocumentor\DomainModel\Version;
use phpDocumentor\DomainModel\Version\Number;

/**
 * Testcase for Version
 * @coversDefaultClass phpDocumentor\Project\Version
 */
class VersionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getVersionNumber
     */
    public function testGetVersion()
    {
        $version = new Version(new Number('1.0.0'));
        $this->assertEquals(new Number('1.0.0'), $version->getVersionNumber());
    }
}
