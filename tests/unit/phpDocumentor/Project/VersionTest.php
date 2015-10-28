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
        $version = new Version(new VersionNumber('1.0.0'));
        $this->assertEquals(new VersionNumber('1.0.0'), $version->getVersionNumber());
    }
}
