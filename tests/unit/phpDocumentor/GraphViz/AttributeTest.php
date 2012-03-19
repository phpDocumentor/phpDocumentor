<?php
/**
 * phpDocumentor
 *
 * PHP Version 5
 *
 * @package   phpDocumentor\Parser\Tests
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpDocumentor-project.org
 */

/**
 * Test for the the class representing a GraphViz attribute.
 *
 * @package phpDocumentor\Graphviz\Tests
 * @author  Mike van Riel <mike.vanriel@naenius.com>
 * @license http://www.opensource.org/licenses/mit-license.php MIT
 * @link    http://phpDocumentor-project.org
 */
class phpDocumentor_GraphViz_AttributeTest extends PHPUnit_Framework_TestCase
{
    /** @var phpDocumentor_GraphViz_Attribute */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new phpDocumentor_GraphViz_Attribute('a', '1');
    }

    /**
     * Tests the getting and setting of the key.
     *
     * @return void
     */
    public function testKey()
    {
        $this->assertSame(
            $this->fixture->getKey(), 'a',
            'Expecting the key to match the initial state'
        );
        $this->assertSame(
            $this->fixture, $this->fixture->setKey('b'),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture->getKey(), 'b',
            'Expecting the key to contain the new value'
        );
    }

    /**
     * Tests the getting and setting of the value.
     *
     * @return void
     */
    public function testValue()
    {
        $this->assertSame(
            $this->fixture->getValue(), '1',
            'Expecting the value to match the initial state'
        );
        $this->assertSame(
            $this->fixture, $this->fixture->setValue('2'),
            'Expecting a fluent interface'
        );
        $this->assertSame(
            $this->fixture->getValue(), '2',
            'Expecting the value to contain the new value'
        );
    }

    /**
     * Tests whether a string starting with a < is recognized as HTML.
     *
     * @return void
     */
    public function testIsValueInHtml()
    {
        $this->fixture->setValue('a');
        $this->assertFalse(
            $this->fixture->isValueInHtml(),
            'Expected value to not be a HTML code'
        );

        $this->fixture->setValue('<a>test</a>');
        $this->assertTrue(
            $this->fixture->isValueInHtml(),
            'Expected value to be recognized as a HTML code'
        );
    }

    /**
     * Tests whether the toString provides a valid GraphViz attribute string.
     *
     * @return void
     */
    public function testToString()
    {
        $this->fixture = new phpDocumentor_GraphViz_Attribute('a', 'b');
        $this->assertSame(
            'a="b"', (string)$this->fixture,
            'Strings should be surrounded with quotes'
        );

        $this->fixture->setValue('a"a');
        $this->assertSame(
            'a="a\"a"', (string)$this->fixture,
            'Strings should be surrounded with quotes'
        );

        $this->fixture->setKey('url');
        $this->assertSame(
            'URL="a\"a"', (string)$this->fixture,
            'The key named URL must be uppercased'
        );

        $this->fixture->setValue('<a>test</a>');
        $this->assertSame(
            'URL=<a>test</a>', (string)$this->fixture,
            'HTML strings should not be surrounded with quotes'
        );
    }
}