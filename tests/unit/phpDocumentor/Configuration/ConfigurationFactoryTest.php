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

use Closure;
use InvalidArgumentException;
use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\Configuration\Factory\Strategy;
use phpDocumentor\Configuration\Factory\Version2;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\Uri;
use SimpleXMLElement;

/**
 * Test case for ConfigurationFactory
 *
 * @coversDefaultClass \phpDocumentor\Configuration\ConfigurationFactory
 * @covers ::<private>
 * @covers ::__construct
 */
final class ConfigurationFactoryTest extends MockeryTestCase
{
    /**
     * @covers ::fromUri
     */
    public function testItLoadsASpecificConfigurationFileUsingTheCorrectStrategy() : void
    {
        $configurationFactory = new ConfigurationFactory(
            [
                new Version3('data/xsd/phpdoc.xsd'),
                new Version2(),
            ],
            []
        );

        $content       = '<phpdocumentor><title>My title</title></phpdocumentor>';
        $configuration = $configurationFactory->fromUri(
            new Uri($this->givenExampleConfigurationFileWithContent($content))
        );

        $this->assertSame('My title', $configuration['phpdocumentor']['title']);
    }
    /**
     * @covers ::fromUri
     */
    public function testLoadingFromUriFailsIfFileDoesNotExist() : void
    {
        $this->expectException(InvalidConfigPathException::class);
        $this->expectExceptionMessage('File file:///does-not-exist could not be found');
        $configurationFactory = new ConfigurationFactory([new Version2()], []);
        $configurationFactory->fromUri(new Uri('/does-not-exist'));
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testThatTheDefaultConfigurationFilesAreLoaded() : void
    {
        $file                 = $this->givenExampleConfigurationFileWithContent(
            '<phpdocumentor><title>My title</title></phpdocumentor>'
        );
        $configurationFactory = new ConfigurationFactory([new Version2()], [$file]);

        $configuration = $configurationFactory->fromDefaultLocations();

        $this->assertSame('My title', $configuration['phpdocumentor']['title']);
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testWhenNoneOfTheDefaultsExistThatTheBakedConfigIsUsed() : void
    {
        $configurationFactory = new ConfigurationFactory([new Version2()], ['does_not_exist.xml']);

        $configuration = $configurationFactory->fromDefaultLocations();

        $this->assertEquals(Version3::buildDefault(), $configuration->getArrayCopy());
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testWhenDefaultFileIsInvalidXMLThenAnExceptionIsRaised() : void
    {
        $file                 = $this->givenExampleConfigurationFileWithContent(
            '<?xml version="1.0" encoding="UTF-8" ?>' .
            '<phpdocumentor xmlns="http://www.phpdoc.org" version="3">' .
            '<foo/>' .
            '</phpdocumentor>'
        );
        $configurationFactory = new ConfigurationFactory(
            [new Version3(__DIR__ . '/../../../../data/xsd/phpdoc.xsd')],
            [$file]
        );

        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage(
            "Element '{http://www.phpdoc.org}foo': This element is not expected. "
            . 'Expected is ( {http://www.phpdoc.org}paths ).'
        );
        $configurationFactory->fromDefaultLocations();
    }

    /**
     * @covers ::addMiddleware
     */
    public function testThatMiddlewaresCanBeAddedAndAreThenApplied() : void
    {
        $inputValue            = ['test'];
        $afterMiddleware1Value = ['test', 'test2'];
        $afterMiddleware2Value = ['test', 'test2', 'test3'];

        $middleWare1 = $this->givenAMiddlewareThatReturns($inputValue, $afterMiddleware1Value);
        $middleWare2 = $this->givenAMiddlewareThatReturns($afterMiddleware1Value, $afterMiddleware2Value);

        $factory = new ConfigurationFactory([$this->givenAValidStrategyThatReturns($inputValue)], []);
        $factory->addMiddleware($middleWare1);
        $factory->addMiddleware($middleWare2);

        $data = $factory->fromUri(new Uri($this->givenExampleConfigurationFileWithContent('<foo></foo>')));

        $this->assertSame($afterMiddleware2Value, $data->getArrayCopy());
    }

    /**
     * @covers ::fromUri
     */
    public function testItHaltsIfNoMatchingStrategyCanBeFound() : void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('No supported configuration files were found');
        $strategies           = []; // No strategy means nothing could match ;)
        $configurationFactory = new ConfigurationFactory($strategies, []);

        $configurationFactory->fromUri(
            new Uri($this->givenExampleConfigurationFileWithContent('<foo></foo>'))
        );
    }

    /**
     * @covers ::__construct
     */
    public function testItErrorsWhenTryingToInitializeWithSomethingOtherThanAStrategy() : void
    {
        $this->expectException('TypeError');
        new ConfigurationFactory(['this_is_not_a_strategy'], []);
    }

    private function givenExampleConfigurationFileWithContent($content) : string
    {
        vfsStream::newFile('foo.xml')
            ->at(vfsStream::setup('dir'))
            ->withContent($content);

        return vfsStream::url('dir/foo.xml');
    }

    private function givenAMiddlewareThatReturns($expectedInputValue, $returnValue) : Closure
    {
        return function ($value) use ($expectedInputValue, $returnValue) {
            $this->assertSame($expectedInputValue, $value);

            return $returnValue;
        };
    }

    private function givenAValidStrategyThatReturns($result) : Strategy
    {
        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('supports')
            ->once()
            ->with(m::type(SimpleXMLElement::class))
            ->andReturn(true);
        $strategy
            ->shouldReceive('convert')
            ->once()
            ->with(m::type(SimpleXMLElement::class))->andReturn($result);

        return $strategy;
    }
}
