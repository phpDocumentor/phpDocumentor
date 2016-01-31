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

use phpDocumentor\DomainModel\Documentation;
use phpDocumentor\DomainModel\Version\Number;

/**
 * Test case for Documentation class.
 *
 * @coversDefaultClass phpDocumentor\Documentation
 * @covers ::<private>
 * @covers ::__construct
 */
final class DocumentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::getVersionNumber
     * @covers ::getDocumentGroups
     * @uses phpDocumentor\Project\VersionNumber
     */
    public function testGetVersion()
    {
        $documentation = new Documentation(new Number('1.0.0'));
        $this->assertEquals(new Number('1.0.0'), $documentation->getVersionNumber());
        $this->assertInternalType('array', $documentation->getDocumentGroups());
    }

    /**
     * @covers ::getVersionNumber
     * @covers ::getDocumentGroups
     * @uses phpDocumentor\Project\VersionNumber
     */
    public function testGetDocumentGroups()
    {
        $documentGroups = array('dummy');
        $documentation = new Documentation(new Number('1.0.0'), $documentGroups);
        $this->assertEquals(new Number('1.0.0'), $documentation->getVersionNumber());
        $this->assertEquals($documentGroups, $documentation->getDocumentGroups());
    }
}
