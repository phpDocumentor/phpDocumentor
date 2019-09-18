<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Configuration;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery as m;
use org\bovigo\vfs\vfsStream;
use phpDocumentor\Configuration\Factory\Strategy;
use phpDocumentor\Configuration\Factory\Version2;
use phpDocumentor\Configuration\Factory\Version3;
use phpDocumentor\Configuration\Exception\InvalidConfigPathException;
use phpDocumentor\Uri;

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
    public function testItLoadsASpecificConfigurationFileUsingTheCorrectStrategy()
    {
        $configurationFactory = new ConfigurationFactory(
            [
                new Version3('data/xsd/phpdoc.xsd'),
                new Version2()
            ],
            []
        );

        $content = '<phpdocumentor><title>My title</title></phpdocumentor>';
        $configuration = $configurationFactory->fromUri(
            new Uri($this->givenExampleConfigurationFileWithContent($content))
        );

        $this->assertSame('My title', $configuration['phpdocumentor']['title']);
    }
    /**
     * @covers ::fromUri
     */
    public function testLoadingFromUriFailsIfFileDoesNotExist()
    {
        $this->expectException(InvalidConfigPathException::class);
        $this->expectExceptionMessage('File file:///does-not-exist could not be found');
        $configurationFactory = new ConfigurationFactory([new Version2()], []);
        $configurationFactory->fromUri(new Uri('/does-not-exist'));
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testThatTheDefaultConfigurationFilesAreLoaded()
    {
        $file = $this->givenExampleConfigurationFileWithContent(
            '<phpdocumentor><title>My title</title></phpdocumentor>'
        );
        $configurationFactory = new ConfigurationFactory([new Version2()], [$file]);

        $configuration = $configurationFactory->fromDefaultLocations();

        $this->assertSame('My title', $configuration['phpdocumentor']['title']);
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testWhenNoneOfTheDefaultsExistThatTheBakedConfigIsUsed()
    {
        $configurationFactory = new ConfigurationFactory([new Version2()], ['does_not_exist.xml']);

        $configuration = $configurationFactory->fromDefaultLocations();

        $this->assertEquals(Version3::buildDefault(), $configuration->getArrayCopy());
    }

    /**
     * @covers ::fromDefaultLocations()
     */
    public function testWhenDefaultFileIsInvalidXMLThenAnExceptionIsRaised()
    {
        $file = $this->givenExampleConfigurationFileWithContent(
          '<?xml version="1.0" encoding="UTF-8" ?>' .
          '<phpdocumentor xmlns="http://www.phpdoc.org" version="3">' .
            '<foo/>' .
          '</phpdocumentor>'
        );
        $configurationFactory = new ConfigurationFactory(
          [new Version3(__DIR__ . '/../../../../data/xsd/phpdoc.xsd')],
          [$file]
        );

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("Element '{http://www.phpdoc.org}foo': This element is not expected. Expected is ( {http://www.phpdoc.org}paths ).");
        //
        $configurationFactory->fromDefaultLocations();
    }

    /**
     * @covers ::addMiddleware
     */
    public function testThatMiddlewaresCanBeAddedAndAreThenApplied()
    {
        $inputValue = ['test'];
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
     * @expectedException \Exception
     * @expectedExceptionMessage No supported configuration files were found
     */
    public function testItHaltsIfNoMatchingStrategyCanBeFound()
    {
        $strategies = []; // No strategy means nothing could match ;)
        $configurationFactory = new ConfigurationFactory($strategies, []);

        $configurationFactory->fromUri(
            new Uri($this->givenExampleConfigurationFileWithContent('<foo></foo>'))
        );
    }

    /**
     * @covers ::__construct
     * @expectedException \TypeError
     */
    public function testItErrorsWhenTryingToInitializeWithSomethingOtherThanAStrategy()
    {
        new ConfigurationFactory(['this_is_not_a_strategy'], []);
    }

    private function givenExampleConfigurationFileWithContent($content): string
    {
        vfsStream::newFile('foo.xml')
            ->at(vfsStream::setup('dir'))
            ->withContent($content);

        return vfsStream::url('dir/foo.xml');
    }

    private function givenAMiddlewareThatReturns($expectedInputValue, $returnValue): \Closure
    {
        return function ($value) use ($expectedInputValue, $returnValue) {
            $this->assertSame($expectedInputValue, $value);

            return $returnValue;
        };
    }

    private function givenAValidStrategyThatReturns($result): Strategy
    {
        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy->shouldReceive('supports')
            ->once()
            ->with(m::type(\SimpleXMLElement::class))
            ->andReturn(true);
        $strategy
            ->shouldReceive('convert')
            ->once()
            ->with(m::type(\SimpleXMLElement::class))->andReturn($result);

        return $strategy;
    }
}
