<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2016 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser\Version;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\Version\Number
 * @covers ::<private>
 * @covers ::__construct
 */
class NumberTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::getVersion
     */
    public function itShouldReturnTheProvidedVersionNumber()
    {
        $versionNumber = new Number('1.0.0');

        $this->assertSame('1.0.0', $versionNumber->getVersion());
    }

    /**
     * @test
     * @covers ::__toString
     */
    public function itShouldProvideTheVersionNumberWhenCastToString()
    {
        $versionNumber = new Number('1.0.0');

        $this->assertSame('1.0.0', (string)$versionNumber);
    }
}
