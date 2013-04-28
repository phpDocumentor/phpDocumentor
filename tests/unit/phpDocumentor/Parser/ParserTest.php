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
 *
 * @covers phpDocumentor\Parser\Parser
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
     * @covers phpDocumentor\Parser\Parser::getIgnoredTags
     * @covers phpDocumentor\Parser\Parser::setIgnoredTags
     */
    public function testSetAndGetIgnoredTags()
    {
        $parser = new Parser();
        $this->assertEquals(array(), $parser->getIgnoredTags());

        $parser->setIgnoredTags(array('param'));
        $this->assertEquals(array('param'), $parser->getIgnoredTags());
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
        $this->assertEquals(ltrim(__FILE__, '/'), $this->fixture->getRelativeFilename(__FILE__));

        // after setting the current directory as root folder; should strip all
        // but filename
        $this->fixture->setPath(dirname(__FILE__));
        $this->assertEquals(basename(__FILE__), $this->fixture->getRelativeFilename(__FILE__));

        // when providing a file in a lower directory it cannot parse and thus
        // it is invalid
        $this->setExpectedException('InvalidArgumentException');
        $this->fixture->getRelativeFilename(realpath(dirname(__FILE__) . '/../phpunit.xml'));
    }

    /**
     * Make sure the setter can transform string to array and set correct attribute
     *
     * @covers phpDocumentor\Parser\Parser::setVisibility
     * @covers phpDocumentor\Parser\Parser::getVisibility
     *
     * @return void
     */
    public function testSetVisibilityCorrectlySetsAttribute()
    {
        $visibility = array('public', 'protected', 'private');

        $this->fixture->setVisibility(implode(',', $visibility));

        $this->assertAttributeEquals($visibility, 'visibility', $this->fixture);
        $this->assertEquals($visibility, $this->fixture->getVisibility());
    }

    /**
     * Tests whether the exporter defaults to a predefined exporter if none is provided and whether one can be set
     * using setExporter.
     *
     * @covers phpDocumentor\Parser\Parser::setExporter
     * @covers phpDocumentor\Parser\Parser::getExporter
     *
     * @return void
     */
    public function testSetAndGetExporter()
    {
        $this->markTestIncomplete('Setter is temporary disabled');
        $parser = new Parser();

        $this->assertInstanceOf('phpDocumentor\Parser\Exporter\ExporterAbstract', $parser->getExporter());

        $exporter_mock = $this->getMock('phpDocumentor\Parser\Exporter\ExporterAbstract', array(), array($parser));
        $parser->setExporter($exporter_mock);

        $this->assertSame($exporter_mock, $parser->getExporter());
    }

    /**
     * Tests whether setting the package name is persisted.
     *
     * @covers phpDocumentor\Parser\Parser::setDefaultPackageName
     * @covers phpDocumentor\Parser\Parser::getDefaultPackageName
     *
     * @return void
     */
    public function testSetAndGetDefaultPackageName()
    {
        $parser = new Parser();

        $this->assertEquals('Default', $parser->getDefaultPackageName());

        $parser->setDefaultPackageName('test');

        $this->assertSame('test', $parser->getDefaultPackageName());
    }
}
