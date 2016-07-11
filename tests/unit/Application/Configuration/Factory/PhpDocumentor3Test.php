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

namespace phpDocumentor\Application\Configuration\Factory;

/**
 * Test case for PhpDocumentor3
 *
 * @coversDefaultClass phpDocumentor\Application\Configuration\Factory\PhpDocumentor3
 */
final class PhpDocumentor3Test extends \PHPUnit_Framework_TestCase
{
    /** @var string */
    private $dataFolder = '';

    /** @var PhpDocumentor3Converter */
    private $strategy;

    public function setUp()
    {
        $this->strategy   = new PhpDocumentor3Converter(__DIR__ . '/../../../../../data/xsd/phpdoc.xsd');
        $this->dataFolder = __DIR__ . '/../../../../../tests/data/';
        require_once($this->dataFolder . 'phpDocumentor3ExpectedArrays.php');
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc3XmlToAnArray()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XML.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItUsesTheDefaultTemplateIfNoneIsFoundInThePhpdoc3Xml()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithoutTemplate.xml');

        $array = $this->strategy->convert($xml);

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
        $xml = <<<XML
<phpdocumentor
    version="3"
    xmlns="http://www.phpdoc.org" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.phpdoc.org phpdoc.xsd">
</phpdocumentor>
XML;

        $xml = new \SimpleXMLElement($xml);

        $this->strategy->convert($xml);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc3Xml()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithoutValues.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleVersionsInThePhpdoc3Xml()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleVersions.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleApisInThePhpdoc3Xml()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleApis.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleApis(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleGuidesInThePhpdoc3Xml()
    {
        $xml = $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleGuides.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleGuides(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleTemplatesInThePhpdoc3Xml()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleTemplates.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleTemplates(), $array);
    }

    /**
     * @covers ::match
     */
    public function testItMatchesWhenVersionIs3()
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XML.xml');

        $bool = $this->strategy->match($xml);

        $this->assertTrue($bool);
    }

    /**
     * @param $file
     *
     * @return \SimpleXMLElement
     */
    private function givenXmlFromFile($file)
    {
        return new \SimpleXMLElement($this->dataFolder . $file, 0, true);
    }
}
