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

namespace phpDocumentor\Transformer\Configuration\Transformations;

/**
 * Test for a template configuration definition.
 */
class TemplateTest extends \PHPUnit_Framework_TestCase
{
    const EXAMPLE_NAME = 'name';

    /** @var Template */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Template(self::EXAMPLE_NAME);
    }

    /**
     * @covers phpDocumentor\Transformer\Configuration\Transformations\Template::__construct
     * @covers phpDocumentor\Transformer\Configuration\Transformations\Template::getName
     */
    public function testIfNameCanBeRetrieved()
    {
        $this->assertSame(self::EXAMPLE_NAME, $this->fixture->getName());
    }
}
