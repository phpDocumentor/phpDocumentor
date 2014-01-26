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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

/**
 * Test file for the File entry type.
 */
class FileTest extends \PHPUnit_Framework_TestCase
{
    public function testAddingAFilename()
    {
        $file = new File();
        $file->setFilename('test');

        $this->assertSame('test', $file->getFilename());
    }
}
