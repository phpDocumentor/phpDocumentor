<?php

namespace phpDocumentor;

use Mockery as m;
use phpDocumentor\ConfigurationFactory\Strategy;

require_once(__DIR__ . '/../../../tests/data/phpDocumentor3ExpectedArrays.php');

/**
 * Test case for ConfigurationFactory
 *
 * @coversDefaultClass phpDocumentor\ConfigurationFactory
 */
final class ConfigurationFactoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAReadableUri()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        chmod($uri, 000);

        new ConfigurationFactory([], $uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriThatIsAFile()
    {
        $uri = new Uri(sys_get_temp_dir());

        new ConfigurationFactory([], $uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriWithContent()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));

        new ConfigurationFactory([], $uri);
    }

    /**
     * @covers ::get
     * @expectedException \Exception
     * @expectedExceptionMessage No strategy found that matches the configuration xml
     */
    public function testItHaltsIfNoMatchingStrategyCanBeFound()
    {
        $xml = <<<XML
<phpdocumentor
    version="2"
    xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd">
</phpdocumentor>
XML;

        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, $xml);

        $uri = new Uri($path);

        $configurationFactory = new ConfigurationFactory([], $uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItRegistersStrategies()
    {
        /** @var m\Mock $strategy */
        $strategy = m::mock(Strategy::class);
        $strategy
            ->shouldReceive('match')
            ->withArgs([\SimpleXMLElement::class])
            ->once()
            ->andReturn(true);

        $strategy
            ->shouldReceive('convert')
            ->withArgs([\SimpleXMLElement::class])
            ->once();

        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<foo>
</foo>
XML;

        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, $xml);

        $uri = new Uri($path);

        $configurationFactory = new ConfigurationFactory([$strategy], $uri);
        $configurationFactory->get();
    }
}
