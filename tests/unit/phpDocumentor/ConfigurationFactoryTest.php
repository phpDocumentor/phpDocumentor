<?php

namespace phpDocumentor;

require_once(__DIR__ . '/../../../tests/data/phpDocumentor2ExpectedArray.php');
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
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriThatIsAFile()
    {
        $uri = new Uri(sys_get_temp_dir());
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriWithContent()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsValidXml()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        file_put_contents($uri, 'foo');
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage Root element name should be phpdocumentor, foo found
     */
    public function testItOnlyAcceptsAllowedXmlStructure()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        $xml  = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<foo>
</foo>
XML;

        file_put_contents($path, $xml);

        $uri = new Uri($path);
        new ConfigurationFactory($uri);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc2XmlToAnArray()
    {
        $uri   = new Uri(__DIR__ . '/../../../tests/data/phpdoc.tpl.xml');
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc3XmlToAnArray()
    {
        $uri   = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XML.xml');
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItUsesTheDefaultTemplateIfNoneIsFoundInThePhpdoc3Xml()
    {
        $uri   = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XMLWithoutTemplate.xml');
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage Element '{http://www.phpdoc.org}phpdocumentor': Missing child element(s). Expected is
     *                           ( {http://www.phpdoc.org}paths ).
     */
    public function testItOnlyAcceptsAValidPhpdoc3XmlStructure()
    {
        $xml  = <<<XML
<phpdocumentor
    version="3"
    xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd">
</phpdocumentor>
XML;
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, $xml);

        $uri = new Uri($path);
        $xml = new ConfigurationFactory($uri);
        $xml->convert();
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc3Xml()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, '<phpdocumentor></phpdocumentor>');

        $uri   = new Uri($path);
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleVersionsInThePhpdoc3Xml()
    {
        $uri   = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XMLWithMultipleVersions.xml');
        $xml   = new ConfigurationFactory($uri);
        $array = $xml->convert();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }
}
