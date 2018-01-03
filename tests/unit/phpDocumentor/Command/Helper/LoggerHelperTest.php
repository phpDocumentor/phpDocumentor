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
use PHPUnit\Framework\TestCase;
use Pimple\Container;
use Psr\Log\LogLevel;
use stdClass;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Zend\I18n\Translator\Translator;

/**
 * Testcase for LoggerHelper
 * @coversDefaultClass \phpDocumentor\Command\Helper\LoggerHelper
 */
class LoggerHelperTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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

    /** @var m\Mock */
    private $eventDispatcherMock;

    protected function setUp()
    {
        $containerMock = m::mock(Container::class);
        $translator = m::mock(Translator::class);
        $translator->shouldReceive('translate')
            ->zeroOrMoreTimes()
            ->andReturnUsing(
                function ($message) {
                    return $message;
                }
            );

        $containerMock->shouldReceive('offsetGet')
            ->with('translator')
            ->andReturn($translator);

        $this->eventDispatcherMock = m::mock(EventDispatcherInterface::class);

        $containerMock->shouldReceive('offsetGet')
            ->with('event_dispatcher')
            ->andReturn($this->eventDispatcherMock);

        $this->fixture = new LoggerHelper($containerMock);
        $this->config = new Configuration();
        $this->config->getLogging()->setLevel(static::MY_LOGLEVEL);
        $this->config->getLogging()->setPaths(array('default' => static::MY_DEFAULT_LOG_PATH));
    }

    /**
     * Assure that addOption is called once
     *
     * @covers ::addOptions
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
            return $closure instanceof Closure;
        };

        $this->eventDispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(
                array(
                    'parser.file.pre',
                    m::on($assertClosure)
                )
            );

        $this->eventDispatcherMock->shouldReceive('addListener')
            ->once()
            ->withArgs(
                array(
                    'system.log',
                    m::on($assertClosure)
                )
            );

        $this->fixture->connectOutputToLogging(
            m::mock('Symfony\Component\Console\Output\OutputInterface')
        );

        // call for a second time.
        $this->fixture->connectOutputToLogging(
            m::mock('Symfony\Component\Console\Output\OutputInterface')
        );

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
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('writeln')
            ->with('  <error>my first message with 2 replacements</error>')
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $event = new LogEvent($this);
        $event->setPriority(LogLevel::ERROR);
        $event->setMessage('my %s message with %d replacements');
        $event->setContext(array(
            'first',
            2,
        ));

        $this->fixture->logEvent($output, $event);
    }

    /**
     * Assure nothing is logged when priority is not matching
     *
     * @covers ::logEvent
     */
    public function testLogPriorityIsChecked()
    {
        $output = m::mock('Symfony\Component\Console\Output\OutputInterface')
            ->shouldReceive('writeln')
            ->never()
            ->shouldReceive('getVerbosity')
            ->andReturn(LogLevel::ERROR)
            ->getMock();

        $event = new LogEvent($this);
        $event->setPriority(LogLevel::DEBUG);

        $this->fixture->logEvent($output, $event);
    }
}
