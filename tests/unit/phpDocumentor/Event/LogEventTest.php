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

namespace phpDocumentor\Event;

use Psr\Log\LogLevel;

/**
 * Test for the LogEvent class.
 */
class LogEventTest extends \PHPUnit_Framework_TestCase
{
    /** @var LogEvent $fixture */
    protected $fixture;

    /**
     * Sets up a fixture.
     */
    protected function setUp()
    {
        $this->fixture = new LogEvent(new \stdClass());
    }

    /**
     * @covers phpDocumentor\Event\LogEvent::getPriority
     */
    public function testHasPriorityInfoByDefault()
    {
        $priority = LogLevel::INFO;

        $this->assertSame($priority, $this->fixture->getPriority());
    }

    /**
     * @covers phpDocumentor\Event\LogEvent::setPriority
     */
    public function testOverridePriorityWithAnother()
    {
        $priority = LogLevel::ALERT;

        $this->fixture->setPriority($priority);

        $this->assertSame($priority, $this->fixture->getPriority());
    }
}
