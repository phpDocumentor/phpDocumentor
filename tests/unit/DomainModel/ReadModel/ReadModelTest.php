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
 * @coversDefaultClass phpDocumentor\DomainModel\ReadModel\ReadModel
 * @covers ::<private>
 */
final class ReadModelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @covers ::__construct
     * @covers ::getName
     * @covers ::getData
     */
    public function itRegistersTheDefinitionAndData()
    {
        $definition = new Definition('abc', new Type('all'));
        $data = 'content';

        $readModel = new ReadModel($definition, $data);

        $this->assertSame($definition->getName(), $readModel->getName());
        $this->assertSame($data, $readModel->getData());
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::__invoke
     */
    public function itReturnsTheDataWhenUsedAsCallable()
    {
        $definition = new Definition('abc', new Type('all'));
        $data = 'content';

        $readModel = new ReadModel($definition, $data);

        $this->assertSame($data, $readModel());
    }
}
