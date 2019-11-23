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
 * Test case for Version2
 *
 * @coversDefaultClass \phpDocumentor\Configuration\Factory\Version2
 */
final class Version2Test extends TestCase
{
    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc2XmlToAnArray() : void
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../data/phpdoc.tpl.xml', 0, true);

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::<private>
     */
    public function testItOnlyAcceptsAllowedXmlStructure() : void
    {
        $this->expectException('Exception');
        $this->expectExceptionMessage('Root element name should be phpdocumentor, foo found');
        $xml = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<foo>
</foo>
XML;

        $xml = new \SimpleXMLElement($xml);

        $version2 = new Version2();
        $version2->convert($xml);
    }

    /**
     * @covers ::supports
     */
    public function testItMatchesWhenVersionIsEmpty() : void
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../data/phpdoc.tpl.xml', 0, true);

        $version2 = new Version2();
        $bool = $version2->supports($xml);

        $this->assertTrue($bool);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItRevertsToDefaultsIfValuesAreNotInTheConfigurationFile() : void
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../data/phpDocumentor2XMLWithoutExtensions.xml',
            0,
            true
        );

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleIgnorePathsInThePhpdoc2Xml() : void
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../data/phpDocumentor2XMLWithMultipleIgnorePaths.xml',
            0,
            true
        );

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getArrayWithMultipleIgnorePaths(), $array);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItShouldUseTargetDirectoryFromTransformerForOutput() : void
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../data/phpDocumentor2XMLWithTarget.xml',
            0,
            true
        );

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getCustomTargetConfig(), $array);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItShouldUseDefinedVisibility() : void
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../data/phpDocumentor2XMLWithVisibility.xml',
            0,
            true
        );

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getDefinedVisibility(), $array);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItShouldUseDefinedEncoding() : void
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../data/phpDocumentor2XMLWithEncoding.xml',
            0,
            true
        );

        $version2 = new Version2();
        $array = $version2->convert($xml);

        $this->assertEquals(Version2ExpectedArray::getCustomEncoding(), $array);
    }
}
