<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use org\bovigo\vfs\vfsStream;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\Uri;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Configuration\ConfigurationFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class ConfigurationFactoryTest extends TestCase
{
    /**
     * @covers ::createDefault
     */
    public function testCreatingTheDefaultConfiguration() : void
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
     * @covers ::createDefault
     * @covers ::addMiddleware
     */
    public function testCreatingTheDefaultConfigurationAppliesAnyMiddleware() : void
    {
        $middleware = static function (array $values) {
            return $values + ['anotherExample'];
        };

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createDefault()->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());
        $factory->addMiddleware($middleware);

        $response = $factory->createDefault();

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration + ['anotherExample'], $response->getArrayCopy());
    }

    /**
     * @uses \phpDocumentor\Configuration\ConfigurationFactory::fromUri
     *
     * @covers ::fromDefaultLocations
     */
    public function testCreatingAConfigurationByScanningTheDefaultLocations() : void
    {
        // only create the actual configuration file phpdoc.xml, explicitly do not define phpdoc.dist.xml
        $structure = [
            'project' => [
                'myProject' => ['phpdoc.xml' => 'xml'],
            ],
        ];

        vfsStream::setup('root');
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
    public function testWhenTheDefaultLocationsAreNotFoundCreateDefaultConfiguration() : void
    {
        // explicitly create _no_ configuration file
        $structure = ['project' => ['myProject' => []]];

        vfsStream::setup('root');
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
    public function testCreatingAConfigurationUsingTheGivenUri() : void
    {
        // using __FILE__ so that it passes the file does not exist scenario
        $uri = new Uri('file://' . __FILE__);

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
    public function testCreatingAConfigurationUsingTheGivenUriAppliesAnyMiddleware() : void
    {
        $middleware = static function (array $values) {
            return $values + ['anotherExample'];
        };

        // using __FILE__ so that it passes the file does not exist scenario
        $uri = new Uri('file://' . __FILE__);

        $configuration = ['exampleConfig'];
        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $symfonyConfigFactory->createFromFile($uri)->willReturn($configuration);

        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());
        $factory->addMiddleware($middleware);

        $response = $factory->fromUri($uri);

        $this->assertInstanceOf(Configuration::class, $response);
        $this->assertSame($configuration + ['anotherExample'], $response->getArrayCopy());
    }

    /**
     * @covers ::fromUri
     */
    public function testCreatingAConfigurationUsingTheGivenUriFailsWhenFileDoesNotExist() : void
    {
        $this->expectException(InvalidConfigPathException::class);

        $uri = new Uri('file://does-not-exist');

        $symfonyConfigFactory = $this->prophesize(SymfonyConfigFactory::class);
        $factory = new ConfigurationFactory([], $symfonyConfigFactory->reveal());

        $factory->fromUri($uri);
    }
}
