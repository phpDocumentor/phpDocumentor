<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Version;

/**
 * Test case for VersionNumber
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Version\Number
 */
class VersionNumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getVersion
     */
    public function testGetVersion()
    {
        $versionNumber = new Number('1.0.0');
        $this->assertSame('1.0.0', $versionNumber->getVersion());
    }
}
