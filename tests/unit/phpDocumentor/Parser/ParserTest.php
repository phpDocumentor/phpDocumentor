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

use \Mockery as m;
use phpDocumentor\Reflection\ProjectFactory;

/**
 * Test class for \phpDocumentor\Parser\Parser.
 *
 * @covers phpDocumentor\Parser\Parser
 */
class ParserTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
        ini_set('zend.script_encoding', null);
        $this->fixture = new Parser(
            m::mock(ProjectFactory::class),
            m::mock('Symfony\Component\Stopwatch\Stopwatch')
        );
    }

    /**
     * @covers \phpDocumentor\Parser\Parser::getIgnoredTags
     * @covers \phpDocumentor\Parser\Parser::setIgnoredTags
     */
    public function testSetAndGetIgnoredTags()
    {
        $parser = new Parser(
            m::mock(ProjectFactory::class),
            m::mock('Symfony\Component\Stopwatch\Stopwatch')
        );
        $this->assertEquals(array(), $parser->getIgnoredTags());

        $parser->setIgnoredTags(array('param'));
        $this->assertEquals(array('param'), $parser->getIgnoredTags());
    }

    /**
     * @covers \phpDocumentor\Parser\Parser::setForced
     * @covers \phpDocumentor\Parser\Parser::isForced
     */
    public function testSetAndCheckWhetherParsingIsForced()
    {
        $this->assertEquals(false, $this->fixture->isForced());

        $this->fixture->setForced(true);
        $this->assertEquals(true, $this->fixture->isForced());
    }

    /**
     * @covers \phpDocumentor\Parser\Parser::setEncoding
     * @covers \phpDocumentor\Parser\Parser::getEncoding
     */
    public function testSettingAndRetrievingTheEncodingOfTheProvidedFiles()
    {
        $this->assertEquals('utf-8', $this->fixture->getEncoding());

        $this->fixture->setEncoding('iso-8859-1');
        $this->assertEquals('iso-8859-1', $this->fixture->getEncoding());
    }

    /**
     * @covers phpDocumentor\Parser\Parser::setPath
     * @covers phpDocumentor\Parser\Parser::getPath
     */
    public function testSettingAndRetrievingTheBasePath()
    {
        // Arrange
        $this->assertSame('', $this->fixture->getPath());

        // Act
        $this->fixture->setPath(sys_get_temp_dir());

        // Assert
        $this->assertSame(sys_get_temp_dir(), $this->fixture->getPath());
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
     * @covers \phpDocumentor\Parser\Parser::setMarkers
     * @covers \phpDocumentor\Parser\Parser::getMarkers
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
     * @covers \phpDocumentor\Parser\Parser::setDefaultPackageName
     * @covers \phpDocumentor\Parser\Parser::getDefaultPackageName
     */
    public function testSetAndGetDefaultPackageName()
    {
        $parser = new Parser(
            m::mock(ProjectFactory::class),
            m::mock('Symfony\Component\Stopwatch\Stopwatch')
        );

        $this->assertEquals('Default', $parser->getDefaultPackageName());

        $parser->setDefaultPackageName('test');

        $this->assertSame('test', $parser->getDefaultPackageName());
    }
}
