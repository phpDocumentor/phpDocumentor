<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin;

/**
 * Tests the functionality for the Parameter class.
 */
class ParameterTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_KEY = 'key123';
    const EXAMPLE_VALUE = 'value123';

    /** @var Parameter $fixture */
    protected $fixture;

    /**
     * Creates a new (empty) fixture object.
     */
    protected function setUp()
    {
        $this->fixture = new Parameter();
    }

    /**
     * @covers phpDocumentor\Plugin\Parameter::getKey
     */
    public function testGetKey()
    {
        $this->assertNull($this->fixture->getKey());

        $property = new \ReflectionProperty('phpDocumentor\Plugin\Parameter', 'key');
        $property->setAccessible(true);
        $property->setValue($this->fixture, self::EXAMPLE_KEY);
        $property->setAccessible(false);

        $this->assertSame(self::EXAMPLE_KEY, $this->fixture->getKey());
    }

    /**
     * @covers phpDocumentor\Plugin\Parameter::getValue
     */
    public function testGetValue()
    {
        $this->assertNull($this->fixture->getValue());

        $property = new \ReflectionProperty('phpDocumentor\Plugin\Parameter', 'value');
        $property->setAccessible(true);
        $property->setValue($this->fixture, self::EXAMPLE_VALUE);
        $property->setAccessible(false);

        $this->assertSame(self::EXAMPLE_VALUE, $this->fixture->getValue());
    }
}
