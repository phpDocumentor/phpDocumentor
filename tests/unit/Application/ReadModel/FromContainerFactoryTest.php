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

namespace phpDocumentor\Application\ReadModel;

use Interop\Container\ContainerInterface;
use Mockery as m;
use phpDocumentor\DomainModel\ReadModel\Type;

/**
 * @coversDefaultClass phpDocumentor\Application\ReadModel\FromContainerFactory
 * @covers ::<private>
 */
final class FromContainerFactoryTest extends \PHPUnit_Framework_TestCase
{
    /** @var ContainerInterface|m\MockInterface */
    private $container;

    /** @var FromContainerFactory */
    private $factory;

    protected function setUp()
    {
        $this->container = m::mock(ContainerInterface::class);
        $this->factory = new FromContainerFactory($this->container, [ 'alias' => 'phpDocumentor2' ]);
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::create
     */
    public function itShouldCreateANewReadModelByRetrievingItFromTheContainer()
    {
        $expectedReadModelName = 'phpDocumentor2';
        $expected = 'readModel';
        $this->container->shouldReceive('get')->once()
            ->with($expectedReadModelName)->andReturn($expected);

        $this->assertSame($expected, $this->factory->create(new Type($expectedReadModelName)));
    }

    /**
     * @test
     * @covers ::__construct
     * @covers ::create
     */
    public function itShouldCreateANewReadModelByRetrievingItFromTheContainerBasedOnAnAlias()
    {
        $expectedReadModelName = 'phpDocumentor2';
        $expected = 'readModel';
        $this->container->shouldReceive('get')->once()
            ->with($expectedReadModelName)->andReturn($expected);

        $this->assertSame($expected, $this->factory->create(new Type('alias')));
    }
}
