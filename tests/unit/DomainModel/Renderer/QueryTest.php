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

namespace phpDocumentor\DomainModel\Renderer;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\Renderer\Query
 * @covers ::<private>
 */
final class QueryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::expression
     */
    public function itRegistersTheExpressionForAQuery()
    {
        $expression = 'expression';

        $query = new Query($expression);

        $this->assertSame($expression, $query->expression());
    }
}
