<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Cli\Command\Helper;

use Mockery as m;
use phpDocumentor\Application\Cli\Command\Command;
use phpDocumentor\Configuration;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use PHPUnit_Framework_TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Tests for the phpDocumentor LoggerHelper class.
 *
 * @coversDefaultClass phpDocumentor\Application\Cli\Command\Helper\LoggerHelper
 */
class LoggerHelperTest extends PHPUnit_Framework_TestCase
{
    /** @var Dispatcher */
    private $dispatcherMock;

    /** @var LoggerHelper */
    protected $fixture;

    const MY_LOGLEVEL = 'loglevel';

    const MY_DEFAULT_LOG_PATH = 'defaultPath';

    protected function setUp()
    {
        $this->dispatcherMock = m::mock(Dispatcher::class);
        $this->fixture        = new LoggerHelper($this->dispatcherMock);
    }

    /**
     * Assure that name of the helper isn't changed
     *
     * @covers ::getName
     */
    public function testGetName()
    {
        $this->assertSame('phpdocumentor_logger', $this->fixture->getName());
    }

    /**
     * Assure that name of the helper isn't changed
     *
     * @covers ::connectOutputToLogging
     */
    public function testConnectOutputToLoggingExecutedOnce()
    {
        $assertClosure = function ($closure) {
            return $closure instanceof \Closure;
        };

        $commandMock = m::mock(Command::class)
            ->shouldReceive('getService')
            ->with('event_dispatcher')
            ->andReturnSelf()
            ->getMock();

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(['parser.file.isCached', m::on($assertClosure)]);

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(['parser.file.analyzed', m::on($assertClosure)]);

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(['system.log', m::on($assertClosure)]);

        $this->fixture->connectOutputToLogging(m::mock(OutputInterface::class), $commandMock);

        // call for a second time.
        $this->fixture->connectOutputToLogging(m::mock(OutputInterface::class), $commandMock);

        //test passes by mockery assertion
        $this->assertTrue(true);
    }

    /**
     * test replacement of placeholders in log message
     *
     * @covers ::logEvent
     */
    public function testLogEventPlaceHoldersAreReplaced()
    {
        $output = m::mock(OutputInterface::class)
            ->shouldReceive('writeln')
            ->with('  <error>my first message with 2 replacements</error>')
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $command = m::mock(Command::class)
            ->shouldReceive('getContainer')->andReturnSelf()
            ->shouldReceive('offsetGet')->andReturnSelf()
            ->getMock();

        $event = new LogEvent($this);
        $event->setPriority(LogLevel::ERROR);
        $event->setMessage('my %s message with %d replacements');
        $event->setContext([
            'first',
            2,
        ]);

        $this->fixture->logEvent($output, $event, $command);
    }

    /**
     * Assure nothing is logged when priority is not matching
     *
     * @covers ::logEvent
     */
    public function testLogPriorityIsChecked()
    {
        $output = m::mock(OutputInterface::class)
            ->shouldReceive('writeln')
            ->never()
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $command = m::mock(Command::class);

        $event = new LogEvent($this);
        $event->setPriority(LogLevel::DEBUG);

        $this->fixture->logEvent($output, $event, $command);
    }
}
