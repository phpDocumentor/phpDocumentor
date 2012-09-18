<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2012 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */
namespace phpDocumentor\Parser;

/**
 * Test class for \phpDocumentor\Parser\Parser.
 */
class ParserTest extends \PHPUnit_Framework_TestCase
{
    /** @var \phpDocumentor\Parser\Parser */
    protected $fixture = null;

    /**
     * Instantiates a new parser object as fixture.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new Parser();
    }

    /**
     * Tests whether the isForced method correctly functions.
     *
     * @covers phpDocumentor\Parser\Parser::setForced
     * @covers phpDocumentor\Parser\Parser::isForced
     *
     * @return void
     */
    public function testForced()
    {
        // defaults to false
        $this->assertEquals(false, $this->fixture->isForced());

        $xml = new \SimpleXMLElement('<project></project>');
        $xml->addAttribute('version', \phpDocumentor\Application::VERSION);

        $this->fixture->setExistingXml($xml->asXML());
        $this->assertEquals(false, $this->fixture->isForced());

        // if version differs, we force a rebuild
        $xml['version'] = \phpDocumentor\Application::VERSION . 'a';
        $this->fixture->setExistingXml($xml->asXML());
        $this->assertEquals(true, $this->fixture->isForced());

        // switching back should undo the force
        $xml['version'] = \phpDocumentor\Application::VERSION;
        $this->fixture->setExistingXml($xml->asXML());
        $this->assertEquals(false, $this->fixture->isForced());

        // manually setting forced should result in a force
        $this->fixture->setForced(true);
        $this->assertEquals(true, $this->fixture->isForced());

        $this->fixture->setForced(false);
        $this->assertEquals(false, $this->fixture->isForced());
    }

    /**
     * Tests whether the doValidation() and setValidate methods function
     * properly.
     *
     * @covers phpDocumentor\Parser\Parser::setValidate
     * @covers phpDocumentor\Parser\Parser::doValidation
     *
     * @return void
     */
    public function testValidate()
    {
        // defaults to false
        $this->assertEquals(false, $this->fixture->doValidation());

        $this->fixture->setValidate(true);
        $this->assertEquals(true, $this->fixture->doValidation());

        $this->fixture->setValidate(false);
        $this->assertEquals(false, $this->fixture->doValidation());
    }

    /**
     * Tests whether the getMarker() and setMarkers methods function
     * properly.
     *
     * @covers phpDocumentor\Parser\Parser::setMarkers
     * @covers phpDocumentor\Parser\Parser::getMarkers
     *
     * @return void
     */
    public function testMarkers()
    {
        $fixture_data = array('FIXME', 'TODO', 'DOIT');

        // default is TODO and FIXME
        $this->assertEquals(array('TODO', 'FIXME'), $this->fixture->getMarkers());

        $this->fixture->setMarkers($fixture_data);
        $this->assertEquals($fixture_data, $this->fixture->getMarkers());
    }

    /**
     * Tests whether the getExistingXml() and setExistingXml() methods function
     * properly.
     *
     * @covers phpDocumentor\Parser\Parser::setExistingXml
     * @covers phpDocumentor\Parser\Parser::getExistingXml
     *
     * @return void
     */
    public function testExistingXml()
    {
        $this->assertEquals(null, $this->fixture->getExistingXml());

        $this->fixture->setExistingXml(
            '<?xml version="1.0" ?><project version="1.0"></project>'
        );

        $this->assertInstanceOf('DOMDocument', $this->fixture->getExistingXml());
        $this->assertEquals(
            '1.0',
            $this->fixture->getExistingXml()->documentElement
                ->getAttribute('version')
        );
    }

    /**
     * Tests whether the getRelativeFilename() and setPath() methods function
     * properly.
     *
     * @covers phpDocumentor\Parser\Parser::setPath
     * @covers phpDocumentor\Parser\Parser::getRelativeFilename
     *
     * @return void
     */
    public function testPathHandling()
    {
        // default is only stripping the opening slash
        $this->assertEquals(
            ltrim(__FILE__, '/'), $this->fixture->getRelativeFilename(__FILE__)
        );

        // after setting the current directory as root folder; should strip all
        // but filename
        $this->fixture->setPath(dirname(__FILE__));
        $this->assertEquals(
            basename(__FILE__), $this->fixture->getRelativeFilename(__FILE__)
        );

        // when providing a file in a lower directory it cannot parse and thus
        // it is invalid
        $this->setExpectedException('InvalidArgumentException');
        $this->fixture->getRelativeFilename(
            realpath(dirname(__FILE__) . '/../phpunit.xml')
        );
    }

    /**
     * Make sure the setter can transform string to array and set correct attribute
     *
     * @covers \phpDocumentor\Parser\Parser::setVisibility
     *
     * @return void
     */
    public function testSetVisibilityCorrectlySetsAttribute()
    {
        $this->fixture->setVisibility('public,protected,private');

        $this->assertAttributeEquals(
            array('public', 'protected', 'private'), 'visibility', $this->fixture
        );
    }
}