<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Event;

use Psr\Log\LogLevel;

/**
 * Test for the DebugEvent class.
 *
 * @coversDefaultClass phpDocumentor\Event\DebugEvent
 */
class DebugEventTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var DebugEvent $fixture */
    protected $fixture;

    protected function setUp()
    {
        $this->fixture = new DebugEvent(new \stdClass());
    }

    /**
     * @covers ::getMessage
     * @covers ::setMessage
     */
    public function testHavingAMessage()
    {
        $message = 'test';

        $this->assertNull($this->fixture->getMessage());

        $this->fixture->setMessage($message);

        $this->assertSame($message, $this->fixture->getMessage());
    }

    /**
     * @covers ::getPriority
     */
    public function testAlwaysHasPriorityDebug()
    {
        $priority = LogLevel::DEBUG;

        $this->assertSame($priority, $this->fixture->getPriority());
    }

    /**
     * @covers ::setPriority
     * @covers ::getPriority
     */
    public function testSetAndGetPriority()
    {
        $priority = LogLevel::INFO;
        $this->fixture->setPriority(LogLevel::INFO);

        $this->assertSame($priority, $this->fixture->getPriority());
    }

    /**
     * @covers ::getContext
     * @covers ::setContext
     */
    public function testSupplyAContextArrayForTheMessage()
    {
        $context = array('test' => 'test2');

        $this->assertSame(array(), $this->fixture->getContext());

        $this->fixture->setContext($context);

        $this->assertSame($context, $this->fixture->getContext());
    }
}
