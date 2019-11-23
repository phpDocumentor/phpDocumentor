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

namespace phpDocumentor\Configuration\Factory;

use PHPUnit\Framework\TestCase;

/**
 * Test case for Version3
 *
 * @coversDefaultClass \phpDocumentor\Configuration\Factory\Version3
 */
final class Version3Test extends TestCase
{
    /** @var string */
    private $dataFolder = '';

    /** @var Version3 */
    private $strategy;

    protected function setUp(): void
    {
        $this->strategy = new Version3(__DIR__ . '/../../../../../data/xsd/phpdoc.xsd');
        $this->dataFolder = __DIR__ . '/../../../data/';
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc3XmlToAnArray() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XML.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItUsesTheDefaultTemplateIfNoneIsFoundInThePhpdoc3Xml() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithoutTemplate.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getDefaultArray(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     *                           ( {http://www.phpdoc.org}paths ).
     */
    public function testItOnlyAcceptsAValidPhpdoc3XmlStructure() : void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Element \'{http://www.phpdoc.org}phpdocumentor\': Missing child element(s). Expected is');
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
    public function testItSetsDefaultValuesIfNoneAreFoundInThePhpdoc3Xml() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithoutValues.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleVersionsInThePhpdoc3Xml() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleVersions.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleApisInThePhpdoc3Xml() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleApis.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getArrayWithMultipleApis(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleGuidesInThePhpdoc3Xml() : void
    {
        $xml = $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleGuides.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getArrayWithMultipleGuides(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleTemplatesInThePhpdoc3Xml() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XMLWithMultipleTemplates.xml');

        $array = $this->strategy->convert($xml);

        $this->assertEquals(Version3ExpectedArrays::getArrayWithMultipleTemplates(), $array);
    }

    /**
     * @covers ::supports
     */
    public function testItMatchesWhenVersionIs3() : void
    {
        $xml = $this->givenXmlFromFile('phpDocumentor3XML.xml');

        $bool = $this->strategy->supports($xml);

        $this->assertTrue($bool);
    }

    private function givenXmlFromFile(string $file): \SimpleXMLElement
    {
        return new \SimpleXMLElement($this->dataFolder . $file, 0, true);
    }
}
