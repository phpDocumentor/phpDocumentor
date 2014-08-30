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

namespace phpDocumentor\Parser;

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
     * @covers phpDocumentor\Parser\Configuration::getDefaultPackageName
     */
    public function testIfDefaultPackageNameIsReturned()
    {
        $this->assertSame('global', $this->fixture->getDefaultPackageName());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration::getEncoding
     */
    public function testIfEncodingIsReturned()
    {
        $this->assertSame('utf-8', $this->fixture->getEncoding());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration::getExtensions
     */
    public function testIfExtensionsIsReturned()
    {
        $this->assertSame(array('php', 'php3', 'phtml'), $this->fixture->getExtensions());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration::getMarkers
     */
    public function testIfMarkersIsReturned()
    {
        $this->assertSame(array('TODO', 'FIXME'), $this->fixture->getMarkers());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration::getVisibility
     */
    public function testIfVisibilityIsReturned()
    {
        $this->assertSame('public,protected,private', $this->fixture->getVisibility());
    }

    /**
     * @covers phpDocumentor\Parser\Configuration::getTarget
     */
    public function testIfTargetIsReturned()
    {
        // Arrange
        $expected = 'Target';

        $property = new \ReflectionProperty('phpDocumentor\Parser\Configuration', 'target');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expected);
        $property->setAccessible(false);

        // Act && Assert
        $this->assertSame($expected, $this->fixture->getTarget());
    }
}
