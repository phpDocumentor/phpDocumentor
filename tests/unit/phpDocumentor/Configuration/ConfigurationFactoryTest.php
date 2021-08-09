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

namespace phpDocumentor\Configuration;

use League\Uri\Contracts\UriInterface;
use League\Uri\Uri;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\ConfigurationFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class ConfigurationFactoryTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @covers ::createDefault
     */
    public function testCreatingTheDefaultConfiguration(): void
    {
        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createDefault()->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());

        $response = $factory->createDefault();

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration, $response->getArrayCopy());
    }

    /**
     * Creating a default configuration is used to create a baseline and can be used in middlewares.
     *
     * If we execute a middleware as part of this step then we end up in an infinite loop.
     *
     * @uses \phpDocumentor\Configuration\ConfigurationFactory::addMiddleware
     *
     * @covers ::createDefault
     */
    public function testCreatingTheDefaultConfigurationDoesNotApplyAnyMiddleware(): void
    {
        $middleware = new class implements MiddlewareInterface
        {
            public function __invoke(Configuration $values, ?UriInterface $uri = null): Configuration
            {
                return $values + ['anotherExample'];
            }
        };

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createDefault()->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());
        $factory->addMiddleware($middleware);

        $response = $factory->createDefault();

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration, $response->getArrayCopy());
    }

    /**
     * @uses \phpDocumentor\Configuration\ConfigurationFactory::fromUri
     *
     * @covers ::fromDefaultLocations
     */
    public function testCreatingAConfigurationByScanningTheDefaultLocations(): void
    {
        // only create the actual configuration file phpdoc.xml, explicitly do not define phpdoc.dist.xml
        $structure = [
            'project' => [
                'myProject' => ['phpdoc.xml' => 'xml'],
            ],
        ];

        vfsStream::setup();
        vfsStream::create($structure);

        // have the application search for both phpdoc.dist.xml and phpdoc.xml; the former doesn't exist so it should
        // use the second
        $distUrl = vfsStream::url('root/project/myProject/phpdoc.dist.xml');
        $configUrl = vfsStream::url('root/project/myProject/phpdoc.xml');

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createFromFile($configUrl)->willReturn($configuration);

        $factory = new ConfigurationFactory([$distUrl, $configUrl], $symfonyConfigFactory->reveal());

        $response = $factory->fromDefaultLocations();

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration, $response->getArrayCopy());
    }

    /**
     * @uses \phpDocumentor\Configuration\ConfigurationFactory::createDefault
     *
     * @covers ::fromDefaultLocations
     */
    public function testWhenTheDefaultLocationsAreNotFoundCreateDefaultConfiguration(): void
    {
        // explicitly create _no_ configuration file
        $structure = ['project' => ['myProject' => []]];

        vfsStream::setup();
        vfsStream::create($structure);

        // both of these do not exist
        $distUrl = vfsStream::url('root/project/myProject/phpdoc.dist.xml');
        $configUrl = vfsStream::url('root/project/myProject/phpdoc.xml');

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createDefault()->willReturn($configuration);

        $factory = new ConfigurationFactory([$distUrl, $configUrl], $symfonyConfigFactory->reveal());

        $response = $factory->fromDefaultLocations();

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration, $response->getArrayCopy());
    }

    /**
     * @covers ::fromUri
     */
    public function testCreatingAConfigurationUsingTheGivenUri(): void
    {
        // using __FILE__ so that it passes the file does not exist scenario
        $uri = Uri::createFromString(__FILE__);

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createFromFile($uri)->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());

        $response = $factory->fromUri($uri);

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration, $response->getArrayCopy());
    }

    /**
     * @covers ::fromUri
     * @covers ::addMiddleware
     */
    public function testCreatingAConfigurationUsingTheGivenUriAppliesAnyMiddleware(): void
    {
        $middleware = new class implements MiddlewareInterface
        {
            public function __invoke(Configuration $values, ?UriInterface $uri = null): Configuration
            {
                $values['newKey'] = 'anotherExample';

                return $values;
            }
        };

        // using __FILE__ so that it passes the file does not exist scenario
        $uri = Uri::createFromString(__FILE__);

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createFromFile($uri)->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());
        $factory->addMiddleware($middleware);

        $response = $factory->fromUri($uri);

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration + ['newKey' => 'anotherExample'], $response->getArrayCopy());
    }

    /**
     * @covers ::fromUri
     */
    public function testCreatingAConfigurationUsingTheGivenUriFailsWhenFileDoesNotExist(): void
    {
        $this->expectException(InvalidConfigPathException::class);

        $uri = Uri::createFromString('does-not-exist');

        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());

        $factory->fromUri($uri);
    }
}
