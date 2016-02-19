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
 * @coversDefaultClass phpDocumentor\DomainModel\ReadModel\ReadModels
 * @covers ::<private>
 */
final class ReadModelsTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @test
     * @coversNothing
     */
    public function itIsACollection()
    {
        $this->assertInstanceOf(\ArrayObject::class, new ReadModels());
    }

    /**
     * @test
     * @covers ::offsetSet
     */
    public function itGuardsThatTheGivenValueIsAReadModelWhenAddingNewItems()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $models = new ReadModels();
        $models[] = 'a';
    }

    /**
     * @test
     * @covers ::offsetSet
     */
    public function itCanAddNewReadModels()
    {
        $readModel = $this->givenAReadModel();

        $models = new ReadModels();
        $models[] = $readModel;

        $this->assertSame([$readModel], $models->getArrayCopy());
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itGuardsThatOnlyReadModelsCanBeSuppliedWhenInitializing()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        new ReadModels(['a']);
    }

    /**
     * @test
     * @covers ::__construct
     */
    public function itCanRegisterReadModelsWhenInitializing()
    {
        $readModel = $this->givenAReadModel();

        $readModels = new ReadModels([$readModel]);

        $this->assertSame([$readModel], $readModels->getArrayCopy());
    }

    /**
     * @return ReadModel
     */
    private function givenAReadModel()
    {
        return new ReadModel(new Definition('name', new Type('all')), 'data');
    }
}
