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

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getDefaultArray(), $array);
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
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithoutValues.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithEmptyExtensionsAndMarkers(), $array);
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

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleVersions(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleApisInThePhpdoc3Xml()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithMultipleApis.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleApis(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleGuidesInThePhpdoc3Xml()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithMultipleGuides.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleGuides(), $array);
    }

    /**
     * @covers ::__construct
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleTemplatesInThePhpdoc3Xml()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XMLWithMultipleTemplates.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $array          = $phpDocumentor3->convert($xml);

        $this->assertEquals(\PhpDocumentor3ExpectedArrays::getArrayWithMultipleTemplates(), $array);
    }

    /**
     * @covers ::match
     */
    public function testItMatchesWhenVersionIs3()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../tests/data/phpDocumentor3XML.xml', 0, true);

        $phpDocumentor3 = new PhpDocumentor3('');
        $bool           = $phpDocumentor3->match($xml);

        $this->assertTrue($bool);
    }
}
