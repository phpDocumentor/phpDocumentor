<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Command\Helper;

use Closure;
use Mockery as m;
use Monolog\Logger;
use phpDocumentor\Configuration;
use phpDocumentor\Event\LogEvent;
use PHPUnit_Framework_TestCase;
use Psr\Log\LogLevel;
use stdClass;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Testcase for LoggerHelper
 */
class LoggerHelperTest extends PHPUnit_Framework_TestCase
{
    /**
     *
     * @var LoggerHelper
     */
    protected $fixture;

    /**
     * Fake config class.
     *
     * @var stdClass
     */
    protected $config;

    const MY_LOGLEVEL = 'loglevel';

    const MY_DEFAULT_LOG_PATH = 'defaultPath';

    protected function setUp()
    {
        $this->fixture = new LoggerHelper();
        $this->config = new Configuration();
        $this->config->getLogging()->setLevel(static::MY_LOGLEVEL);
        $this->config->getLogging()->setPaths(array('default' => static::MY_DEFAULT_LOG_PATH));
    }

    /**
     * Assure that addOption is called once
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::addOptions
     */
    public function testAddOptions()
    {
        $commandMock = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('addOption')
            ->once()
            ->withAnyArgs()
            ->getMock();

        $this->fixture->addOptions($commandMock);

        //test passes by mockery assertion
        $this->assertTrue(true);
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
            return $closure instanceof Closure;
        };

        $commandMock = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('getService')
            ->with('event_dispatcher')
            ->andReturnSelf()
            ->getMock();

        $commandMock->shouldReceive('addListener')
            ->once()
            ->withArgs(
                array(
                    'parser.file.pre',
                    m::on($assertClosure)
                )
            );

        $commandMock->shouldReceive('addListener')
            ->once()
            ->withArgs(
                array(
                    'system.log',
                    m::on($assertClosure)
                )
            );

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
            ->shouldReceive('translate')
            ->andReturnUsing(
                function ($message) {
                    return $message;
                }
            )
            ->getMock();

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

    /**
     * Check Loglevel per verbosity
     *
     * @dataProvider verbosityDataProvider
     * @covers phpDocumentor\Command\Helper\LoggerHelper::reconfigureLogger
     */
    public function testReconfigureLoggerVerbosity($verbosity, $expectedLogLevel)
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface')
            ->shouldReceive('getOption')
            ->andReturnNull()
            ->getMock();

        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('getVerbosity')
            ->andReturn($verbosity)
            ->getMock();

        $application = m::mock('phpDocumentor\Application')
            ->shouldReceive('configureLogger')->withArgs(array("", $expectedLogLevel, "defaultPath",))
            ->shouldReceive('offsetGet')->with('config')->andReturn($this->config)
            ->shouldReceive('offsetGet')->andReturnNull()
            ->getMock();

        $command = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('getContainer')
            ->andReturn($application)
            ->getMock();

        $this->fixture->reconfigureLogger($input, $output, $command);
    }

    /**
     * Dataprovider for verbosity tests
     *
     * @return array
     */
    public function verbosityDataProvider()
    {
        return array(
            array(
                OutputInterface::VERBOSITY_QUIET,
                Logger::ERROR
            ),
            array(
                OutputInterface::VERBOSITY_NORMAL,
                self::MY_LOGLEVEL
            ),
            array(
                OutputInterface::VERBOSITY_VERBOSE,
                Logger::WARNING
            ),
            array(
                OutputInterface::VERBOSITY_VERY_VERBOSE,
                Logger::INFO
            ),
            array(
                OutputInterface::VERBOSITY_DEBUG,
                Logger::DEBUG
            ),
        );
    }

    /**
     *
     * @covers phpDocumentor\Command\Helper\LoggerHelper::reconfigureLogger
     */
    public function testLogPathDefaultIsUsed()
    {
        $input = m::mock('Symfony\Component\Console\Input\InputInterface')
            ->shouldReceive('getOption')
            ->andReturnNull()
            ->getMock();

        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('getVerbosity')
            ->andReturn(OutputInterface::VERBOSITY_QUIET)
            ->getMock();

        $application = m::mock('phpDocumentor\Application')
            ->shouldReceive('configureLogger')->withArgs(array(null, Logger::ERROR, static::MY_DEFAULT_LOG_PATH))
            ->shouldReceive('offsetGet')->with('config')->andReturn($this->config)
            ->shouldReceive('offsetGet')->with(m::any())->andReturnNull()
            ->getMock();

        $command = m::mock('phpDocumentor\Command\Command')
            ->shouldReceive('getContainer')
            ->andReturn($application)
            ->getMock();

        $this->fixture->reconfigureLogger($input, $output, $command);
    }
}
