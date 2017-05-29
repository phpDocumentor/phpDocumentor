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
 * Test file for the module.
 */
class ModuleTest extends \PHPUnit_Framework_TestCase
{

    public function testRetrieveModuleRoot()
    {
        $file   = new File();
        $module = new Module($file);

        $this->assertSame($file, $module->getTableOfContentsRoot());
    }
}
