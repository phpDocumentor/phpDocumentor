<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Fileset;

/**
 * Test case for Collection class.
 *
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpdoc.org
 */
class CollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var Collection */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new Collection();
    }

    /**
     * Tests the addDirectory method.
     *
     * @return void
     */
    public function testAddDirectory()
    {
        // instantiate a new instance because we want to be sure it is clean
        $fixture = new Collection();

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
            $fixture->getFilenames()
        );

        // instantiate a new instance because we want to be sure it is clean
        $fixture = new Collection();

        // load the unit test folder
        $fixture->addDirectory(dirname(__FILE__) . '/../../');
        $files = $fixture->getFilenames();
        $count = count($files);

        // do a few checks to see if it has caught some cases
        $this->assertGreaterThan(1, $count);
        $this->assertContains(
            realpath(dirname(__FILE__) . '/../../phpDocumentor/ParserTest.php'),
            $files
        );
        $this->assertContains(
            realpath(
                dirname(__FILE__) . '/../../phpDocumentor/File/CollectionTest.php'
            ),
            $files
        );

        // should exclude 1 less
        $fixture = new Collection();
        $fixture->getIgnorePatterns()->append('*r/ParserTest.php');
        $fixture->addDirectory(dirname(__FILE__) . '/../../');
        $this->assertCount($count -1, $fixture->getFilenames());

        $fixture = new Collection();
        $fixture->getIgnorePatterns()->append('*/phpDocumentor/*');
        $fixture->addDirectory(dirname(__FILE__) . '/../../');
        $this->assertEmpty($fixture->getFilenames());
    }

}