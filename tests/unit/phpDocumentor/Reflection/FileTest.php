<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @author     John Flatness <john@zerocrates.org>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Test class for phpDocumentor_Reflection_File.
 *
 * @category   phpDocumentor
 * @package    Reflection
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @author     John Flatness <john@zerocrates.org>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class phpDocumentor_Reflection_FileTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that a file-level docblock can have no package tag.
     *
     * @return void
     */
    public function testCanHaveFileDocBlockWithoutPackageTag()
    {
        /** @var phpDocumentor_Reflection_File $file */
        $file = new phpDocumentor_Reflection_File(
            dirname(__FILE__) . '/../../../data/NoPackageFileDocBlock.php'
        );
        $file->process();

        /** @var phpDocumentor_Reflection_DocBlock $docBlock */
        $docBlock = $file->getDocBlock();

        $this->assertNotNull($docBlock);
        $this->assertEmpty($docBlock->getTagsByName('package'));
    }
}
