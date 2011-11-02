<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

class DocBlox_Parser_FilesTest extends PHPUnit_Framework_TestCase
{
    /** @var DocBLox_Parser_Files */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new DocBlox_Parser_Files();
    }

    /**
     * Tests the addDirectory method.
     *
     * @return void
     */
    public function testAddDirectory()
    {
        // instantiate a new instance because we want to be sure it is clean
        $fixture = new DocBlox_Parser_Files();

        // read the phar test fixture
        $fixture->addDirectory(
            'phar://'.dirname(__FILE__).'/../../../data/test.phar'
        );

        // we know which files are in there; test against it
        $this->assertEquals(
            array(
                 'phar://' . dirname(__FILE__)
                    . '/../../../data/test.phar/folder/test.php',
                 'phar://' . dirname(__FILE__)
                    . '/../../../data/test.phar/test.php',
            ),
            $fixture->getFiles()
        );

        // instantiate a new instance because we want to be sure it is clean
        $fixture = new DocBlox_Parser_Files();

        // load the unit test folder
        $fixture->addDirectory(dirname(__FILE__) . '/../../');

        // do a few checks to see if it has caught some cases
        $this->assertContains(
            realpath(dirname(__FILE__) . '/../ParserTest.php'),
            $fixture->getFiles()
        );
        $this->assertContains(
            realpath(dirname(__FILE__) . '/../Parser/FilesTest.php'),
            $fixture->getFiles()
        );
    }

}