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

namespace phpDocumentor\Transformer;

use phpDocumentor\Transformer\Configuration\ExternalClassDocumentation;

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
     * @covers phpDocumentor\Transformer\Configuration::getSource
     * @covers phpDocumentor\Transformer\Configuration::setSource
     */
    public function testIfSourceIsReturned()
    {
        // Arrange
        $expected = 'source';

        // Act
        $this->fixture->setSource($expected);
        $result = $this->fixture->getSource();

        // Assert
        $this->assertSame($expected, $result);
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration::getTarget
     */
    public function testIfTargetIsReturned()
    {
        // Arrange
        $expected = 'target';

        $property = new \ReflectionProperty('phpDocumentor\Transformer\Configuration', 'target');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expected);
        $property->setAccessible(false);

        // Act && Assert
        $this->assertSame($expected, $this->fixture->getTarget());
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration::getExternalClassDocumentation
     * @covers phpDocumentor\Transformer\Configuration::setExternalClassDocumentation
     */
    public function testIfExternalClassDocumentationIsSetAndReturned()
    {
        // Arrange
        $expected = array(new ExternalClassDocumentation('prefix', 'uri'));

        // Act
        $this->fixture->setExternalClassDocumentation($expected);
        $result = $this->fixture->getExternalClassDocumentation();

        // Assert
        $this->assertSame($expected, $result);
    }
}
