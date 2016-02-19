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

namespace phpDocumentor\DomainModel\ReadModel;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\ReadModel\Type
 * @covers ::<private>
 */
final class TypeTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::__toString
     */
    public function itRegistersTheTypeName()
    {
        $type = new Type('all');

        $this->assertSame('all', (string)$type);
    }
}
