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
 * Test case for PhpDocumentor2
 *
 * @coversDefaultClass phpDocumentor\Application\Configuration\Factory\PhpDocumentor2
 */
final class PhpDocumentor2Test extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once(__DIR__ . '/../../../../../tests/data/phpDocumentor2ExpectedArray.php');
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItConvertsPhpdoc2XmlToAnArray()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../../tests/data/phpdoc.tpl.xml', 0, true);

        $phpDocumentor2 = new ConfigurationConverter();
        $array          = $phpDocumentor2->convert($xml);

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::<private>
     * @expectedException \Exception
     * @expectedExceptionMessage Root element name should be phpdocumentor, foo found
     */
    public function testItOnlyAcceptsAllowedXmlStructure()
    {
        $xml  = <<<XML
<?xml version="1.0" encoding="UTF-8" ?>
<foo>
</foo>
XML;

        $xml = new \SimpleXMLElement($xml);

        $phpDocumentor2 = new ConfigurationConverter();
        $phpDocumentor2->convert($xml);
    }

    /**
     * @covers ::match
     */
    public function testItMatchesWhenVersionIsEmpty()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../../tests/data/phpdoc.tpl.xml', 0, true);

        $phpDocumentor2 = new ConfigurationConverter();
        $bool = $phpDocumentor2->match($xml);

        $this->assertTrue($bool);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItRevertsToDefaultsIfValuesAreNotInTheConfigurationFile()
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../../../tests/data/phpDocumentor2XMLWithoutExtensions.xml',
            0,
            true
        );

        $phpDocumentor2 = new ConfigurationConverter();
        $array          = $phpDocumentor2->convert($xml);

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getDefaultArray(), $array);
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function testItAcceptsMultipleIgnorePathsInThePhpdoc2Xml()
    {
        $xml = new \SimpleXMLElement(
            __DIR__ . '/../../../../../tests/data/phpDocumentor2XMLWithMultipleIgnorePaths.xml',
            0,
            true
        );

        $phpDocumentor2 = new ConfigurationConverter();
        $array          = $phpDocumentor2->convert($xml);

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getArrayWithMultipleIgnorePaths(), $array);
    }
}
