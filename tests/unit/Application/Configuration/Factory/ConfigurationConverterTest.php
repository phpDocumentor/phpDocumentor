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
 * Test case for ConfigurationConverter
 *
 * @coversDefaultClass phpDocumentor\Application\Configuration\Factory\ConfigurationConverter
 */
final class ConfigurationConverterTest extends \PHPUnit_Framework_TestCase
{
    public function setUp()
    {
        require_once(__DIR__ . '/../../../../../tests/data/phpDocumentor2ExpectedArray.php');
    }

    /**
     * @covers ::convert
     * @covers ::<private>
     */
    public function terstItConvertsPhpdoc2XmlToAnArray()
    {
        $xml = new \SimpleXMLElement(__DIR__ . '/../../../../../tests/data/phpdoc.tpl.xml', 0, true);

        $phpDocumentor2 = new ConfigurationConverter();
        $array          = $phpDocumentor2->convertToLatestVersion($xml);

        $this->assertEquals(\PhpDocumentor2ExpectedArray::getDefaultArray(), $array);
    }
}
