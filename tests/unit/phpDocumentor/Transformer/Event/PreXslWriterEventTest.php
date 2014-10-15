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
 * Tests the functionality for the PreXslWriterEvent class.
 */
class PreXslWriterEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var PreXslWriterEvent $fixture */
    protected $fixture;

    /** @var \DOMElement */
    protected $element;

    /** @var int[] */
    protected $progress = array(0, 0);

    /**
     * Creates a new (empty) fixture object.
     * Creates a new DOMElement object.
     */
    protected function setUp()
    {
        $this->fixture = new PreXslWriterEvent(new \stdClass());
        $this->element = new \DOMElement('root');
    }

    /**
     * @covers phpDocumentor\Transformer\Event\PreXslWriterEvent::getElement
     * @covers phpDocumentor\Transformer\Event\PreXslWriterEvent::setElement
     */
    public function testSetAndGetElement()
    {
        $this->assertSame(null, $this->fixture->getElement());

        $this->fixture->setElement($this->element);

        $this->assertSame($this->element, $this->fixture->getElement());
    }

    /**
     * @covers phpDocumentor\Transformer\Event\PreXslWriterEvent::getProgress
     * @covers phpDocumentor\Transformer\Event\PreXslWriterEvent::setProgress
     */
    public function testSetAndGetProgress()
    {
        $this->assertSame(array(0, 0), $this->fixture->getProgress());

        $this->fixture->setProgress(array(1, 2));

        $this->assertSame(array(1, 2), $this->fixture->getProgress());
    }
}
