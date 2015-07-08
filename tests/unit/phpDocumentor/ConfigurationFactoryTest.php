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
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriThatIsAFile()
    {
        $uri = new Uri(sys_get_temp_dir());
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage String could not be parsed as XML
     */
    public function testItOnlyAcceptsAUriWithContent()
    {
        $uri = new Uri(tempnam(sys_get_temp_dir(), 'foo'));
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
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
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
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
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc2XmlToAnArray()
    {
        $uri                  = new Uri(__DIR__ . '/../../../tests/data/phpdoc.tpl.xml');
        $configurationFactory = new ConfigurationFactory($uri);
        $array                = $configurationFactory->get();

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc3XmlToAnArray()
    {
        $uri                  = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XML.xml');
        $configurationFactory = new ConfigurationFactory($uri);
        $array                = $configurationFactory->get();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItUsesTheDefaultTemplateIfNoneIsFoundInThePhpdoc3Xml()
    {
        $uri                  = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XMLWithoutTemplate.xml');
        $configurationFactory = new ConfigurationFactory($uri);
        $array                = $configurationFactory->get();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::get
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

        $uri                  = new Uri($path);
        $configurationFactory = new ConfigurationFactory($uri);
        $configurationFactory->get();
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc3Xml()
    {
        $path = tempnam(sys_get_temp_dir(), 'foo');
        file_put_contents($path, <<<XML
<phpdocumentor
        version="3"
        xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns="http://www.phpdoc.org"
        xsi:noNamespaceSchemaLocation="phpdoc.xsd"
        >
    <paths>
        <output></output>
        <cache></cache>
    </paths>
    <version number="1.0.0">
        <folder></folder>
        <api format="">
            <source dsn="file://.">
                <path></path>
            </source>
            <ignore>
                <path></path>
            </ignore>
            <extensions>
                <extension></extension>
            </extensions>
            <visibility>public</visibility>
            <markers>
                <marker></marker>
            </markers>
        </api>
        <guide format="">
            <source>
                <path>docs</path>
            </source>
        </guide>
    </version>
    <template name="clean" location="https://github.com/phpDocumentor/phpDocumentor2/tree/develop/data/templates/clean"/>
</phpdocumentor>
XML
);

        $uri                  = new Uri($path);
        $configurationFactory = new ConfigurationFactory($uri);
        $array                = $configurationFactory->get();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::get
     * @covers ::<private>
     */
    public function testItAcceptsMultipleVersionsInThePhpdoc3Xml()
    {
        $uri                  = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XMLWithMultipleVersions.xml');
        $configurationFactory = new ConfigurationFactory($uri);
        $array                = $configurationFactory->get();

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::replaceLocation
     * @covers ::get
     * @covers ::<private>
     */
    public function testItReplacesTheLocationOfTheConfigurationFileIfItIsDifferent()
    {
        $oldUri = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XML.xml');
        $newUri = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XMLWithMultipleVersions.xml');

        $configurationFactory = new ConfigurationFactory($oldUri);

        $array = $configurationFactory->get();
        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);

        $configurationFactory->replaceLocation($newUri);

        $array = $configurationFactory->get();
        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::replaceLocation
     * @covers ::get
     * @covers ::<private>
     */
    public function testItDoesNotReplaceTheLocationOfTheConfigurationFileIfItIsTheSame()
    {
        $uri = new Uri(__DIR__ . '/../../../tests/data/phpDocumentor3XML.xml');

        $configurationFactory = new ConfigurationFactory($uri);

        $array = $configurationFactory->get();
        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);

        $configurationFactory->replaceLocation($uri);

        $array = $configurationFactory->get();
        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }
}
