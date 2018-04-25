<?php

namespace phpDocumentor;

use Mockery as m;
use Monolog\Logger;
use Symfony\Component\Console\Application as ConsoleApplication;

class ApplicationTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
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
        $this->assertArrayHasKey('kernel.timer.start', $this->fixture);
        $this->assertArrayHasKey('kernel.stopwatch', $this->fixture);
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
        $this->assertArrayHasKey('serializer', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfConfigurationIsRegistered()
    {
        $this->assertArrayHasKey('config', $this->fixture);
        $this->assertInstanceOf('phpDocumentor\Configuration', $this->fixture['config']);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     * @covers phpDocumentor\Application::addEventDispatcher
     */
    public function testIfEventDispatcherIsRegistered()
    {
        $this->assertArrayHasKey('event_dispatcher', $this->fixture);
        $this->assertInstanceOf('phpDocumentor\Event\Dispatcher', $this->fixture['event_dispatcher']);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfTranslatorIsRegistered()
    {
        $this->assertArrayHasKey('translator', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfDescriptorBuilderIsRegistered()
    {
        $this->assertArrayHasKey('descriptor.builder', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfParserIsRegistered()
    {
        $this->assertArrayHasKey('parser', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfPartialsAreRegistered()
    {
        $this->assertArrayHasKey('partials', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfTransformerIsRegistered()
    {
        $this->assertArrayHasKey('transformer', $this->fixture);
    }

    /**
     * @covers phpDocumentor\Application::__construct
     */
    public function testIfPluginsAreRegistered()
    {
        $this->assertArrayHasKey('checkstyle', $this->fixture['transformer.writer.collection']);
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
        $logger = m::mock('Monolog\Logger')
            ->shouldReceive('pushHandler')->with(m::on(function ($stream) use ($expectedLogLevel) {
                if (!$stream instanceof \Monolog\Handler\StreamHandler) {
                    return false;
                }

                return $stream->getLevel() === $expectedLogLevel;
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
        return [
            [
                'emergency',
                Logger::EMERGENCY,
            ],
            [
                'emerg',
                Logger::EMERGENCY,
            ],
            [
                'alert',
                Logger::ALERT,
            ],
            [
                'critical',
                Logger::CRITICAL,
            ],
            [
                'error',
                Logger::ERROR,
            ],
            [
                'err',
                Logger::ERROR,
            ],
            [
                'warning',
                Logger::WARNING,
            ],
            [
                'warn',
                Logger::WARNING,
            ],
            [
                'notice',
                Logger::NOTICE,
            ],
            [
                'info',
                Logger::INFO,
            ],
            [
                'debug',
                Logger::DEBUG,
            ],
        ];
    }
}
