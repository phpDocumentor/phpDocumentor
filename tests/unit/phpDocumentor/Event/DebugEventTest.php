<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Event;

use Psr\Log\LogLevel;

/**
 * Test for the DebugEvent class.
 */
class DebugEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var DebugEvent $fixture */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new DebugEvent(new \stdClass());
    }

    /**
     * @covers phpDocumentor\Event\DebugEvent::getMessage
     * @covers phpDocumentor\Event\DebugEvent::setMessage
     */
    public function testHavingAMessage()
    {
        $message = 'test';

        $this->assertNull($this->fixture->getMessage());

        $this->fixture->setMessage($message);

        $this->assertSame($message, $this->fixture->getMessage());
    }

    /**
     * @covers phpDocumentor\Event\DebugEvent::getPriority
     */
    public function testAlwaysHasPriorityDebug()
    {
        $priority = LogLevel::DEBUG;

        $this->assertSame($priority, $this->fixture->getPriority());
    }

    /**
     * @covers phpDocumentor\Event\DebugEvent::getContext
     * @covers phpDocumentor\Event\DebugEvent::setContext
     */
    public function testSupplyAContextArrayForTheMessage()
    {
        $context = array('test' => 'test2');

        $this->assertSame(array(), $this->fixture->getContext());

        $this->fixture->setContext($context);

        $this->assertSame($context, $this->fixture->getContext());
    }
}
