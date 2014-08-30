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

namespace phpDocumentor;

use phpDocumentor\Partials\Partial;
use phpDocumentor\Plugin\Plugin;

class ConfigurationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Configuration */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Configuration();
    }

    /**
     * @covers phpDocumentor\Configuration::getTitle
     */
    public function testIfTitleIsReturned()
    {
        // Arrange
        $expected = 'title';

        $property = new \ReflectionProperty('phpDocumentor\Configuration', 'title');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expected);
        $property->setAccessible(false);

        // Act && Assert
        $this->assertSame($expected, $this->fixture->getTitle());
    }

    /**
     * @covers phpDocumentor\Configuration::getPlugins
     */
    public function testIfPluginsAreReturned()
    {
        // Arrange
        $expected = array(new Plugin('className'));

        $property = new \ReflectionProperty('phpDocumentor\Configuration', 'plugins');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expected);
        $property->setAccessible(false);

        // Act && Assert
        $this->assertSame($expected, $this->fixture->getPlugins());
    }

    /**
     * @covers phpDocumentor\Configuration::getPartials
     */
    public function testIfPartialsAreReturned()
    {
        // Arrange
        $expected = array(new Partial());

        $property = new \ReflectionProperty('phpDocumentor\Configuration', 'partials');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expected);
        $property->setAccessible(false);

        // Act && Assert
        $this->assertSame($expected, $this->fixture->getPartials());
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getFiles
     */
    public function testIfFilesConfigurationIsReturned()
    {
        $this->assertInstanceOf('phpDocumentor\Parser\Configuration\Files', $this->fixture->getFiles());
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getLogging
     */
    public function testIfLoggingConfigurationIsReturned()
    {
        $this->assertInstanceOf('phpDocumentor\Configuration\Logging', $this->fixture->getLogging());
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getParser
     */
    public function testIfParserConfigurationIsReturned()
    {
        $this->assertInstanceOf('phpDocumentor\Parser\Configuration', $this->fixture->getParser());
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getTransformations
     */
    public function testIfTransformationsConfigurationIsReturned()
    {
        $this->assertInstanceOf(
            'phpDocumentor\Transformer\Configuration\Transformations',
            $this->fixture->getTransformations()
        );
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getTransformer
     */
    public function testIfTransformerConfigurationIsReturned()
    {
        $this->assertInstanceOf('phpDocumentor\Transformer\Configuration', $this->fixture->getTransformer());
    }

    /**
     * @covers phpDocumentor\Configuration::__construct
     * @covers phpDocumentor\Configuration::getTranslator
     */
    public function testIfTranslatorConfigurationIsReturned()
    {
        $this->assertInstanceOf('phpDocumentor\Translator\Configuration', $this->fixture->getTranslator());
    }
}
