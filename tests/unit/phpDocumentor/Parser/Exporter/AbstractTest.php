<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */

/**
 * Mock for the Layer superclass in the phpDocumentor_Parser Component.
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_AbstractMock
    extends phpDocumentor_Parser_Exporter_Abstract
{
    public function export(phpDocumentor_Reflection_File $file)
    {
    }

    public function getContents()
    {
    }
}

/**
 * Test for the Parser Exporter base class.
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class phpDocumentor_Parser_Exporter_AbstractTest
    extends PHPUnit_Framework_TestCase
{
    /** @var phpDocumentor_Parser_Exporter_AbstractMock */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $parser = $this->getMock('phpDocumentor_Parser');
        $this->fixture = new \phpDocumentor_Parser_Exporter_AbstractMock($parser);
    }

    public function testIncludeSource()
    {
        $this->assertFalse($this->fixture->getIncludeSource());
        $this->fixture->setIncludeSource(true);
        $this->assertTrue($this->fixture->getIncludeSource());
    }

}