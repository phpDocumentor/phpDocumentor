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

use Mockery as m;
use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\Parser\Version\Number;

/**
 * @coversDefaultClass phpDocumentor\DomainModel\ReadModel\Factory
 * @covers ::<private>
 * @covers ::__construct
 */
final class FactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var Mapper\Factory|m\MockInterface */
    private $mapperFactory;

    /** @var Factory */
    private $factory;

    public function setUp()
    {
        $this->mapperFactory = m::mock(Mapper\Factory::class);
        $this->factory = new Factory($this->mapperFactory);
    }

    /**
     * @test
     * @covers ::create
     */
    public function itCreatesAReadModelFromADefinitionAndDocumentation()
    {
        $type = new Type('all');
        $modelName = 'name';
        $data = 'data';
        $readModelDefinition = new Definition($modelName, $type);
        $documentation = new Documentation(new Number('1.0'));

        $mapper = m::mock(Mapper::class);
        $mapper->shouldReceive('create')->with($readModelDefinition, $documentation)->andReturn($data);
        $this->mapperFactory->shouldReceive('create')->with($type)->andReturn($mapper);

        $resultingReadModel = $this->factory->create($readModelDefinition, $documentation);

        $this->assertInstanceOf(ReadModel::class, $resultingReadModel);
        $this->assertSame($modelName, $resultingReadModel->getName());
        $this->assertSame($data, $resultingReadModel->getData());
    }

    /**
     * @test
     * @covers ::create
     */
    public function itCanApplyFiltersWhenCreatingAReadModel()
    {
        $filters = [
            function ($data) {
                return $data . ', more data';
            }
        ];

        $mapper = m::mock(Mapper::class);
        $mapper->shouldReceive('create')->andReturn('data');
        $this->mapperFactory->shouldReceive('create')->andReturn($mapper);

        $resultingReadModel = $this->factory->create(
            new Definition('name', new Type('all'), $filters),
            new Documentation(new Number('1.0'))
        );

        $this->assertInstanceOf(ReadModel::class, $resultingReadModel);
        $this->assertSame('data, more data', $resultingReadModel->getData());
    }

    /**
     * @test
     * @covers ::create
     */
    public function itShouldThrowAnExceptionIfNoMapperWasFound()
    {
        $this->setExpectedException(\InvalidArgumentException::class);

        $this->mapperFactory->shouldReceive('create')->andReturnNull();

        $this->factory->create(
            new Definition('name', new Type('all')),
            new Documentation(new Number('1.0'))
        );
    }
}
