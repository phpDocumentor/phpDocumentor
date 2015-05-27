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

namespace phpDocumentor\Command\Helper;

use Mockery as m;
use phpDocumentor\Configuration;
use phpDocumentor\Event\Dispatcher;
use phpDocumentor\Event\LogEvent;
use phpDocumentor\Translator\Translator;
use PHPUnit_Framework_TestCase;
use Psr\Log\LogLevel;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Testcase for LoggerHelper
 */
class LoggerHelperTest extends PHPUnit_Framework_TestCase
{
    private $translatorMock;
    /** @var Dispatcher */
    private $dispatcherMock;

    /** @var LoggerHelper */
    protected $fixture;

    const MY_LOGLEVEL = 'loglevel';

    const MY_DEFAULT_LOG_PATH = 'defaultPath';

    protected function setUp()
    {
        $this->dispatcherMock = m::mock(Dispatcher::class);
        $this->translatorMock = m::mock(Translator::class);
        $this->fixture = new LoggerHelper($this->dispatcherMock, $this->translatorMock);
    }

    /**
     * Assure that name of the helper isn't changed
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::getName
     */
    public function testGetName()
    {
        $this->assertSame('phpdocumentor_logger', $this->fixture->getName());
    }

    /**
     * Assure that name of the helper isn't changed
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::connectOutputToLogging
     */
    public function testConnectOutputToLoggingExecutedOnce()
    {
        $assertClosure = function ($closure) {
            return $closure instanceof \Closure;
        };

        $commandMock = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('getService')
            ->with('event_dispatcher')
            ->andReturnSelf()
            ->getMock();

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(array('parser.file.isCached', m::on($assertClosure)));

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(array('parser.file.analyzed', m::on($assertClosure)));

        $this->dispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(array('system.log', m::on($assertClosure)));

        $this->fixture->connectOutputToLogging(
            m::mock('Symfony\Component\Console\Output\OutputInterface'),
            $commandMock
        );

        // call for a second time.
        $this->fixture->connectOutputToLogging(
            m::mock('Symfony\Component\Console\Output\OutputInterface'),
            $commandMock
        );

        //test passes by mockery assertion
        $this->assertTrue(true);
    }

    /**
     * test replacement of placeholders in log message
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::logEvent
     */
    public function testLogEventPlaceHoldersAreReplaced()
    {
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('writeln')
            ->with('  <error>my first message with 2 replacements</error>')
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $command = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('getContainer')->andReturnSelf()
            ->shouldReceive('offsetGet')->andReturnSelf()
            ->getMock();

        $this->translatorMock
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($message) {
                    return $message;
                }
            );


        $event = new LogEvent($this);
        $event->setPriority(LogLevel::ERROR);
        $event->setMessage('my %s message with %d replacements');
        $event->setContext(array(
            'first',
            2,
        ));

        $this->fixture->logEvent($output, $event, $command);
    }

    /**
     * Assure nothing is logged when priority is not matching
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::logEvent
     */
    public function testLogPriorityIsChecked()
    {
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('writeln')
            ->never()
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $command = m::mock('phpDocumentor\Command\Command');

        $event = new LogEvent($this);
        $event->setPriority(LogLevel::DEBUG);

        $this->fixture->logEvent($output, $event, $command);
    }
}
