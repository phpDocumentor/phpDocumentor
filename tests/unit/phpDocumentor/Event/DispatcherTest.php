<?php

declare(strict_types=1);

/**
 * phpDocumentor2
 */

namespace phpDocumentor\Event;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;

/**
 * Test for the Dispatcher class.
 */
class DispatcherTest extends MockeryTestCase
{
    /**
     * @covers \phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testFactoryMethodReturnsInstanceOfSelf() : void
    {
        $this->assertInstanceOf('phpDocumentor\Event\Dispatcher', Dispatcher::getInstance());
    }

    /**
     * @covers \phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testDefaultDispatcherAlwaysReturnsTheSameInstance() : void
    {
        $fixture = Dispatcher::getInstance();
        $this->assertSame($fixture, Dispatcher::getInstance());
    }

    /**
     * @covers \phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testNamedDispatcherAlwaysReturnsTheSameInstance() : void
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertSame($fixture, Dispatcher::getInstance('mine'));
    }

    /**
     * @covers \phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testDifferentNamesForDispatcherReturnsADifferentInstance() : void
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertNotSame($fixture, Dispatcher::getInstance('default'));
    }

    /**
     * @covers \phpDocumentor\Event\Dispatcher::setInstance
     * @covers \phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testInstancesCanBeOverridden() : void
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertSame($fixture, Dispatcher::getInstance('mine'));

        $newObject = m::mock('phpDocumentor\Event\Dispatcher');
        Dispatcher::setInstance('mine', $newObject);
        $this->assertSame($newObject, Dispatcher::getInstance('mine'));
    }
}
