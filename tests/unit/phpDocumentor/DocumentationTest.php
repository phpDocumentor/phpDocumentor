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


use phpDocumentor\Project\VersionNumber;

/**
 * Test case for Documentation class.
 *
 * @coversDefaultClass phpDocumentor\Documentation
 */
class DocumentationTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::getTitle
     */
    public function testGetTitle()
    {
        $documentation = new Documentation('MyTitle', new VersionNumber('1.0.0'));
        $this->assertEquals('MyTitle', $documentation->getTitle());
    }

    /**
     * @covers ::__construct
     * @covers ::getVersionNumber
     */
    public function testGetVersion()
    {
        $documentation = new Documentation('MyTitle', new VersionNumber('1.0.0'));
        $this->assertEquals(new VersionNumber('1.0.0'), $documentation->getVersionNumber());
    }

    /**
     * @covers ::__construct
     * @covers ::getVersionNumber
     */
    public function testGetDocumentGroups()
    {
        $documentGroups = array('dummy');
        $documentation = new Documentation('MyTitle', new VersionNumber('1.0.0'), $documentGroups);
        $this->assertEquals($documentGroups, $documentation->getDocumentGroups());
    }
}

