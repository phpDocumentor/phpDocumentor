<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.5
 *
 * @copyright 2010-2015 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\DomainModel\Parser;

use Mockery as m;
use phpDocumentor\DomainModel\Parser\Documentation;
use phpDocumentor\DomainModel\Parser\Version\Number;
use Stash\Pool;

/**
 * Test case for DocumentationRepository
 * @coversDefaultClass phpDocumentor\DomainModel\Parser\DocumentationRepository
 */
class DocumentationRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var DocumentationRepository
     */
    private $fixture;

    /**
     * @var m\Mock
     */
    private $cacheMock;

    protected function setUp()
    {
        $this->cacheMock = m::mock(Pool::class);
        $this->fixture = new DocumentationRepository($this->cacheMock);
    }

    /**
     * @covers ::__construct
     * @covers ::findByVersionNumber
     */
    public function testFindByVersionNumberCacheIsValid()
    {
        $this->cacheMock->shouldReceive('getItem')
            ->once()
            ->with('Documentation\1.0.0')
            ->andReturnSelf();

        $this->cacheMock->shouldReceive('getItem->isMiss')
            ->once()
            ->andReturn(false);

        $documentation = new Documentation(new Number('1.0.0'));
        $this->cacheMock->shouldReceive('getItem->get')
            ->once()
            ->andReturn($documentation);

        $this->assertSame(
            $documentation,
            $this->fixture->findByVersionNumber(new Number('1.0.0'))
        );
    }

    /**
     * @covers ::__construct
     * @covers ::findByVersionNumber
     * @covers ::<private>
     */
    public function testFindByVersionNumberCacheInvalid()
    {
        $this->cacheMock->shouldReceive('getItem')
            ->once()
            ->with('Documentation\1.0.1')
            ->andReturnSelf();

        $this->cacheMock->shouldReceive('getItem->isMiss')
            ->once()
            ->andReturn(true);

        $this->assertNull(
            $this->fixture->findByVersionNumber(new Number('1.0.1'))
        );
    }

    /**
     * @covers ::__construct
     * @covers ::save
     * @covers ::<private>
     */
    public function testSave()
    {
        $documentation = new Documentation(new Number('1.0.2'));

        $this->cacheMock->shouldReceive('getItem')
            ->once()
            ->with('Documentation\1.0.2')
            ->andReturnSelf();

        $this->cacheMock->shouldReceive('getItem->lock')
            ->once();

        $this->cacheMock->shouldReceive('getItem->set')
            ->once()
            ->with($documentation);

        $this->fixture->save($documentation);

    }
}
