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

namespace phpDocumentor\Transformer\Event;

/**
 * Tests the functionality for the PreTransformEvent class.
 */
class PreTransformEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var PreTransformEvent $fixture */
    protected $fixture;

    /** @var \DOMDocument */
    protected $source;

    /**
     * Creates a new (empty) fixture object.
     * Creates a new DOMDocument object.
     */
    protected function setUp()
    {
        $this->fixture = new PreTransformEvent(new \stdClass());
        $this->source = new \DOMDocument('1.0', 'UTF-8');
    }

    /**
     * @covers phpDocumentor\Transformer\Event\PreTransformEvent::getSource
     * @covers phpDocumentor\Transformer\Event\PreTransformEvent::setSource
     */
    public function testSetAndGetSource()
    {
        $this->assertSame(null, $this->fixture->getSource());

        $this->fixture->setSource($this->source);

        $this->assertSame($this->source, $this->fixture->getSource());
    }
}
