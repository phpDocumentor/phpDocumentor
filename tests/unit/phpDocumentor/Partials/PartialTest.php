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

namespace phpDocumentor\Partials;

/**
 * Tests for the phpDocumentor\Partials\Partial class.
 */
class PartialTest extends \PHPUnit_Framework_TestCase
{
    /** @var Partial */
    private $fixture;

    /**
     * Initializes the fixture.
     */
    protected function setUp()
    {
        $this->fixture = new Partial;
    }

    /**
     * @covers phpDocumentor\Partials\Partial::getContent
     * @covers phpDocumentor\Partials\Partial::setContent
     */
    public function testGetContent()
    {
        $this->assertSame(null, $this->fixture->getContent());
        $this->fixture->setContent('Foo bar');

        $result = $this->fixture->getContent();

        $this->assertSame('Foo bar', $result);
    }

    /**
     * @covers phpDocumentor\Partials\Partial::getLink
     * @covers phpDocumentor\Partials\Partial::setLink
     */
    public function testGetLink()
    {
        $this->assertSame(null, $this->fixture->getLink());
        $this->fixture->setLink('http://www.phpdoc.org/');

        $result = $this->fixture->getLink();

        $this->assertSame('http://www.phpdoc.org/', $result);
    }

    /**
     * @covers phpDocumentor\Partials\Partial::getName
     * @covers phpDocumentor\Partials\Partial::setName
     */
    public function testGetName()
    {
        $this->assertSame(null, $this->fixture->getName());
        $this->fixture->setName('My name');

        $result = $this->fixture->getName();

        $this->assertSame('My name', $result);
    }
}
