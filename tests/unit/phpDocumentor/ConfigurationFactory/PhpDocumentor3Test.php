<?php

namespace phpDocumentor\ConfigurationFactory;

require_once(__DIR__ . '/../../../../tests/data/phpDocumentor3ExpectedArrays.php');

/**
 * Test case for PhpDocumentor3
 *
 * @coversDefaultClass phpDocumentor\ConfigurationFactory\PhpDocumentor3
 */
final class PhpDocumentor3Test extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc3XmlToAnArray()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XML.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertSame(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItUsesTheDefaultTemplateIfNoneIsFoundInThePhpdoc3Xml()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithoutTemplate.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertSame(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
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
        $xml = <<<XML
<phpdocumentor
    version="3"
    xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd">
</phpdocumentor>
XML;

        $xml = new \SimpleXMLElement($xml);

        $phpDocumentor3 = new PhpDocumentor3('');
        $phpDocumentor3->convert($xml);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc3Xml()
    {
        $xml = <<<XML
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
    <template name="clean"/>
</phpdocumentor>
XML;

        $xml = new \SimpleXMLElement($xml);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertSame(\PhpDocumentor3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleVersionsInThePhpdoc3Xml()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithMultipleVersions.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertSame(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::match
     */
    public function testItMatchesWhenVersionIs3()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XML.xml', 0, true);

        $phpDocumentor2 = new PhpDocumentor3('');
        $bool = $phpDocumentor2->match($xml);

        $this->assertTrue($bool);
    }
}
