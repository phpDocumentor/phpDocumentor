<?php
/**
 * DocBlox
 *
 * PHP Version 5
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @copyright  2010-2011 Mike van Riel / Naenius (http://www.naenius.com)
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */

/**
 * Mock for the Layer superclass in the DocBlox_Parser Component.
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Parser_AbstractMock extends DocBlox_Parser_Abstract
{

}

/**
 * Test for the Layer superclass in the DocBlox_Parser Component.
 *
 * @category   DocBlox
 * @package    Parser
 * @subpackage Tests
 * @author     Mike van Riel <mike.vanriel@naenius.com>
 * @license    http://www.opensource.org/licenses/mit-license.php MIT
 * @link       http://docblox-project.org
 */
class DocBlox_Parser_AbstractTest extends PHPUnit_Framework_TestCase
{
    /** @var DocBLox_Parser_AbstractMock */
    protected $fixture = null;

    /**
     * Initializes the fixture for this test.
     *
     * @return void
     */
    protected function setUp()
    {
        $this->fixture = new DocBlox_Parser_AbstractMock();
    }

    /**
     * Tests the dispatch method.
     *
     * It is expected that the `dispatch` method:
     *
     * * Returns null when no EventDispatcher is set.
     * * Throws a DocBlox_Parser_Exception if the event dispatcher variable
     *   contains an invalid value.
     * * The correct method of the EventDispatcher is used and the return value
     *   is correctly returned.
     *
     * @return void
     */
    public function testDispatch()
    {
        // set up mocks for the dispatcher and the generated event.
        $event_dispatcher = $this->getMock(
            'sfEventDispatcher', array('notify')
        );
        $event = $this->getMock(
            'sfEvent',
            array('getReturnValue'),
            array($this->fixture, 'system.log', array('message' => 'body'))
        );

        // the event dispatcher's notify method will be invoken and return the
        // expected event
        $event_dispatcher
            ->expects($this->once())
            ->method('notify')
            ->will($this->returnValue($event));

        // we will let the event return true to test whether the return value
        // is actually returned
        $event->expects($this->once())
            ->method('getReturnValue')
            ->will($this->returnValue(true));

        // test without setting the dispatcher
        $result = $this->fixture->dispatch('system.log', array('message' => 'body'));
        $this->assertSame(
            null, $result,
            'Expected result to be null when no dispatcher is set'
        );

        // set the dispatcher
        DocBlox_Parser_Abstract::$event_dispatcher = $event_dispatcher;

        // test with the dispatcher
        $result = $this->fixture->dispatch('system.log', array('message' => 'body'));
        $this->assertSame(
            true, $result,
            'Expected result to be true when the dispatcher mock object is set'
        );

        // if the event dispatcher is not null but also no an event dispatcher;
        // throw exception
        $this->setExpectedException('DocBlox_Parser_Exception');
        DocBlox_Parser_Abstract::$event_dispatcher = true;
        $this->fixture->dispatch('system.log', array('message' => 'body'));
    }

    /**
     * Tests the log method.
     *
     * It is expected that the `log` method,
     *
     * * invokes the event dispatcher.
     *
     * @return void
     */
    public function testLog()
    {
        // set up mocks for the dispatcher and the generated event.
        $event_dispatcher = $this->getMock(
            'sfEventDispatcher', array('notify')
        );
        $event = $this->getMock(
            'sfEvent',
            array('getReturnValue'),
            array($this->fixture, 'system.log', array(
                'message' => 'body',
                'priority' => 6
            ))
        );

        // the event dispatcher's notify method will be invoken and return the
        // expected event
        $event_dispatcher
            ->expects($this->once())
            ->method('notify')
            ->will($this->returnValue($event));

        // we will let the event return true to test whether the return value
        // is actually returned
        $event->expects($this->once())
            ->method('getReturnValue')
            ->will($this->returnValue(true));

        // test without setting the dispatcher
        DocBlox_Parser_Abstract::$event_dispatcher = $event_dispatcher;
        $this->fixture->log('body', 6);
    }

    /**
     * Tests the debug method.
     *
     * It is expected that the `debug` method,
     *
     * * invokes the event dispatcher.
     *
     * @return void
     */
    public function testDebug()
    {
        // set up mocks for the dispatcher and the generated event.
        $event_dispatcher = $this->getMock(
            'sfEventDispatcher', array('notify')
        );
        $event = $this->getMock(
            'sfEvent',
            array('getReturnValue'),
            array($this->fixture, 'system.debug', array(
                'message' => 'body'
            ))
        );

        // the event dispatcher's notify method will be invoken and return the
        // expected event
        $event_dispatcher
            ->expects($this->once())
            ->method('notify')
            ->will($this->returnValue($event));

        // we will let the event return true to test whether the return value
        // is actually returned
        $event->expects($this->once())
            ->method('getReturnValue')
            ->will($this->returnValue(true));

        // test without setting the dispatcher
        DocBlox_Parser_Abstract::$event_dispatcher = $event_dispatcher;
        $this->fixture->debug('body');
    }

}