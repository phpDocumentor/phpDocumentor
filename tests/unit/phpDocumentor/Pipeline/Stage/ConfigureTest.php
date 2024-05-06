<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Pipeline\Stage;

use InvalidArgumentException;
use League\Uri\Contracts\UriInterface;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Configuration\ConfigurationFactory;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\LoggerInterface;

use function getcwd;
use function sys_get_temp_dir;

/** @coversDefaultClass \phpDocumentor\Pipeline\Stage\Configure */
final class ConfigureTest extends TestCase
{
    use ProphecyTrait;

    /** @var Locator */
    private $cacheLocator;

    protected function setUp(): void
    {
        $this->cacheLocator = $this->prophesize(Locator::class);
    }

    public function testConfigNoneWillIgnoreFileLoad(): void
    {
        $cachePath = new Path(sys_get_temp_dir() . '/phpdocumentor');
        $config = [
            'phpdocumentor' => ['paths' => ['cache' => $cachePath]],
        ];

        $this->cacheLocator->providePath($cachePath);
        $this->cacheLocator->locate()->willReturn($cachePath);

        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(3);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();
        $configurationFactory->fromDefault()->willReturn(
            new Configuration($config),
        );

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration($config),
            $logger->reveal(),
            $this->cacheLocator->reveal(),
            $this->prophesize(EnvironmentFactory::class)->reveal(),
            getcwd(),
        );

        self::assertEquals($config, $stage(['config' => 'none']));
    }

    public function testInvalidConfigPathWillThrowException(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(3);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();
        $configurationFactory->fromUri()->shouldNotBeCalled();

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal(),
            $this->cacheLocator->reveal(),
            $this->prophesize(EnvironmentFactory::class)->reveal(),
            getcwd(),
        );

        $stage(['config' => 'some/invalid/file.xml']);
    }

    public function testNoConfigOptionWillLoadDefaultFiles(): void
    {
        $cachePath = new Path(sys_get_temp_dir() . '/phpdocumentor');
        $config = [
            'phpdocumentor' => ['paths' => ['cache' => $cachePath]],
        ];

        $this->cacheLocator->providePath($cachePath);
        $this->cacheLocator->locate()->willReturn($cachePath);

        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(3);
        $configurationFactory->fromDefaultLocations()->willReturn(new Configuration($config));
        $configurationFactory->fromUri()->shouldNotBeCalled();

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal(),
            $this->cacheLocator->reveal(),
            $this->prophesize(EnvironmentFactory::class)->reveal(),
            getcwd(),
        );

        $actual = $stage([]);

        $this->assertEquals($config, $actual);
    }

    public function testConfigWithValidFileWillCallFactory(): void
    {
        $cachePath = new Path(sys_get_temp_dir() . '/phpdocumentor');
        $config = [
            'phpdocumentor' => ['paths' => ['cache' => $cachePath]],
        ];

        $this->cacheLocator->providePath($cachePath);
        $this->cacheLocator->locate()->willReturn($cachePath);

        $logger = $this->prophesize(LoggerInterface::class);
        $configurationFactory = $this->prophesize(ConfigurationFactory::class);
        $configurationFactory->addMiddleware(Argument::any())->shouldBeCalledTimes(3);
        $configurationFactory->fromDefaultLocations()->shouldNotBeCalled();
        $configurationFactory->fromUri(Argument::type(UriInterface::class))
            ->willReturn(new Configuration($config));

        $stage = new Configure(
            $configurationFactory->reveal(),
            new Configuration(),
            $logger->reveal(),
            $this->cacheLocator->reveal(),
            $this->prophesize(EnvironmentFactory::class)->reveal(),
            getcwd(),
        );

        $actual = $stage(['config' => __FILE__]);

        $this->assertEquals($config, $actual);
    }
}
