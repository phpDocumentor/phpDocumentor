<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use League\Uri\Contracts\UriInterface;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\ConfigurationFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Configure
 * @covers ::<private>
 * @covers ::__construct
 * @covers ::__invoke
 */
final class ConfigureTest extends TestCase
{
    public function testConfigNoneWillIgnoreFileLoad() : void
    {
        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(2);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal()
        );

        self::assertEquals([], $stage(['config' => 'none']));
    }

    public function testInvalidConfigPathWillThrowException() : void
    {
        $this->expectException(\InvalidArgumentException::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(2);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();
        $configurationFactory->fromUri()->shouldNotBeCalled();

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal()
        );

        $stage(['config' => 'some/invalid/file.xml']);
    }

    public function testNoConfigOptionWillLoadDefaultFiles() : void
    {
        $config = ['test' => 'some config'];
        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(2);
        $configurationFactory->fromDefaultLocations()->willReturn(new Configuration($config));
        $configurationFactory->fromUri()->shouldNotBeCalled();

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal()
        );

        $actual = $stage([]);

        $this->assertEquals($config, $actual);
    }

    public function testConfigWithValidFileWillCallFactory() : void
    {
        $config = ['test' => 'some config'];
        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(2);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();
        $configurationFactory->fromUri(Argument::type(UriInterface::class))
            ->willReturn(new Configuration($config));

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal()
        );

        $actual = $stage(['config' => __FILE__]);

        $this->assertEquals($config, $actual);
    }
}
