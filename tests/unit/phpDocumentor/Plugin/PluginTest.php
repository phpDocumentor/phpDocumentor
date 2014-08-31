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

namespace phpDocumentor\Plugin;

use Mockery as m;

/**
 * Tests for the plugin configuration definition.
 */
class PluginTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_CLASS_NAME = 'className';

    /** @var Plugin */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Plugin(self::EXAMPLE_CLASS_NAME);
    }

    /**
     * @covers phpDocumentor\Plugin\Plugin::getClassName
     */
    public function testRetrieveClassName()
    {
        $this->assertSame(self::EXAMPLE_CLASS_NAME, $this->fixture->getClassName());
    }

    /**
     * @covers phpDocumentor\Plugin\Plugin::getParameters
     */
    public function testGetParameters()
    {
        $this->assertEmpty($this->fixture->getParameters());

        $expectedParameters = array(m::mock('phpDocumentor\Plugin\Plugin'));

        $property = new \ReflectionProperty('phpDocumentor\Plugin\Plugin', 'parameters');
        $property->setAccessible(true);
        $property->setValue($this->fixture, $expectedParameters);
        $property->setAccessible(false);

        $this->assertSame($expectedParameters, $this->fixture->getParameters());
    }
}
