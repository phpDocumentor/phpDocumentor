<?php

namespace phpDocumentor;

use Mockery as m;
use Monolog\Logger;
use Symfony\Component\Console\Application as ConsoleApplication;

class ApplicationTest extends \PHPUnit_Framework_TestCase
{
    /** @var Application */
    private $fixture;

    /**
     * Initializes the fixture for this test.
     */
    public function setUp()
    {
        $this->fixture = new Application();
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfStopwatchIsStarted()
    {
        $this->assertTrue(isset($this->fixture['kernel.timer.start']));
        $this->assertTrue(isset($this->fixture['kernel.stopwatch']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfVersionIsPopulated()
    {
        $this->assertRegExp('/^[\d]+\.[\d]+\.[\d]+(\-[\w]+)?$/', Application::$VERSION);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfAutoloaderIsRegistered()
    {
        // Arrange
        $autoloader = m::mock('Composer\Autoload\ClassLoader');

        // Act
        $application = new Application($autoloader);

        // Assert
        $this->assertSame($autoloader, $application['autoloader']);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfSerializerIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['serializer']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfConfigurationIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['config']));
        $this->assertInstanceOf('phpDocumentor\Configuration', $this->fixture['config']);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     * @covers phpDocumentor\Application::addEventDispatcher
     */
    public function testIfEventDispatcherIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['event_dispatcher']));
        $this->assertInstanceOf('phpDocumentor\Event\Dispatcher', $this->fixture['event_dispatcher']);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfValidatorIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['validator']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfTranslatorIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['translator']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfDescriptorBuilderIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['descriptor.builder']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfParserIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['parser']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfPartialsAreRegistered()
    {
        $this->assertTrue(isset($this->fixture['partials']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfTransformerIsRegistered()
    {
        $this->assertTrue(isset($this->fixture['transformer']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfPluginsAreRegistered()
    {
        $this->assertTrue(isset($this->fixture['transformer.writer.collection']['checkstyle']));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     * @covers phpDocumentor\Application::addCommandsForProjectNamespace
     */
    public function testIfRunCommandIsRegistered()
    {
        /** @var ConsoleApplication $console */
        $console = $this->fixture['console'];
        $this->assertTrue($console->has('project:run'));
    }

    /**
     * @covers phpDocumentor\Application::__construct
     * @covers phpDocumentor\Application::defineIniSettings
     */
    public function testIfMemoryLimitIsDisabled()
    {
        $this->assertSame('-1', ini_get('memory_limit'));
    }

    /**
     * Test setting loglevel to logger.
     *
     * @param string $loglevel
     * @param string $expectedLogLevel
     *
     * @dataProvider loglevelProvider
     * @covers phpDocumentor\Application::configureLogger
     */
    public function testSetLogLevel($loglevel, $expectedLogLevel)
    {
        $logger = m::Mock('Monolog\Logger')
            ->shouldReceive('pushHandler')->with(m::On(function($stream) use($expectedLogLevel) {
               if (!$stream instanceof \Monolog\Handler\StreamHandler) {
                   return false;
               }

               return $stream->getLevel() == $expectedLogLevel;
            }))
            ->shouldReceive('popHandler')
            ->getMock();
        $this->fixture->configureLogger($logger, $loglevel);
        $this->assertSame($expectedLogLevel, $this->fixture['monolog.level']);
    }

    /**
     * Data provider for testSetLogLevel
     *
     * @return array[] 
     */
    public function loglevelProvider()
    {
        return array(
            array(
                'emergency',
                Logger::EMERGENCY,
            ),
            array(
                'emerg',
                Logger::EMERGENCY,
            ),
            array(
                'alert',
                Logger::ALERT,
            ),
            array(
                'critical',
                Logger::CRITICAL,
            ),
            array(
                'error',
                Logger::ERROR,
            ),
            array(
                'err',
                Logger::ERROR,
            ),
            array(
                'warning',
                Logger::WARNING,
            ),
            array(
                'warn',
                Logger::WARNING,
            ),
            array(
                'notice',
                Logger::NOTICE,
            ),
            array(
                'info',
                Logger::INFO,
            ),
            array(
                'debug',
                Logger::DEBUG,
            ),
        );
    }
}
