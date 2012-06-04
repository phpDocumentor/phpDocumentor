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

namespace phpDocumentor\Parser\Exporter;

/**
 * Mock for the Layer superclass in the \phpDocumentor\Parser\Exporter Component.
 *
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://phpdoc.org
 */
class ExporterAbstractMock extends ExporterAbstract
{
    public function export($file)
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
class ExporterAbstractTest extends \PHPUnit_Framework_TestCase
{
    /** @var ExporterAbstractMock */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $parser = $this->getMock('\phpDocumentor\Parser\Parser');
        $this->fixture = new ExporterAbstractMock($parser);
    }

    public function testIncludeSource()
    {
        $this->assertFalse($this->fixture->getIncludeSource());
        $this->fixture->setIncludeSource(true);
        $this->assertTrue($this->fixture->getIncludeSource());
    }

}