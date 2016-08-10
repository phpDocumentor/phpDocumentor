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
 * Test case for Version2To3Converter
 *
 * @coversDefaultClass phpDocumentor\Application\Configuration\Factory\Version2To3Converter
 */
final class Version2To3ConverterTest extends \PHPUnit_Framework_TestCase
{
    public function testItConvertsPhpDocumentor2XmlToAnArray()
    {
        $phpDoc2Xml = new \SimpleXMLElement(__DIR__ . '/../../../../../tests/data/phpdoc.tpl.xml', 0, true);
        $phpDoc3Xml = new \SimpleXMLElement(__DIR__ . '/../../../../../tests/data/phpDocumentor2XMLConvertedToPhpDocumentor3XML.xml', 0, true);

        $converter = new Version2To3Converter();

        $convertedXml = $converter->convert($phpDoc2Xml);

        $this->assertEquals($phpDoc3Xml, $convertedXml);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage Root element of the xml should be phpdocumentor, foo found.
     */
    public function testItAllowsOnlyPhpDocumentorConfigXml()
    {
        $phpDoc2Xml = new \SimpleXMLElement('<foo></foo>');

        $converter = new Version2To3Converter();
        $converter->convert($phpDoc2Xml);
    }
}
