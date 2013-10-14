<?php
/**
 * phpDocumentor2
 */

namespace phpDocumentor\Event;

use Mockery as m;

/**
 * Test for the Dispatcher class.
 */
class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testFactoryMethodReturnsInstanceOfSelf()
    {
        $this->assertInstanceOf('phpDocumentor\Event\Dispatcher', Dispatcher::getInstance());
    }

    /**
     * @covers phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testDefaultDispatcherAlwaysReturnsTheSameInstance()
    {
        $fixture = Dispatcher::getInstance();
        $this->assertSame($fixture, Dispatcher::getInstance());
    }

    /**
     * @covers phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testNamedDispatcherAlwaysReturnsTheSameInstance()
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertSame($fixture, Dispatcher::getInstance('mine'));
    }

    /**
     * @covers phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testDifferentNamesForDispatcherReturnsADifferentInstance()
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertNotSame($fixture, Dispatcher::getInstance('default'));
    }

    /**
     * @covers phpDocumentor\Event\Dispatcher::setInstance
     * @covers phpDocumentor\Event\Dispatcher::getInstance
     */
    public function testInstancesCanBeOverridden()
    {
        $fixture = Dispatcher::getInstance('mine');
        $this->assertSame($fixture, Dispatcher::getInstance('mine'));

        $newObject = m::mock('phpDocumentor\Event\Dispatcher[]');
        Dispatcher::setInstance('mine', $newObject);
        $this->assertSame($newObject, Dispatcher::getInstance('mine'));
    }
}
