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

namespace phpDocumentor\ApiReference;

use Flyfinder\Specification\SpecificationInterface;
use League\Flysystem\Filesystem;
use Mockery as m;
use phpDocumentor\DocumentGroupFormat;
use phpDocumentor\FileSystemFactory;
use phpDocumentor\SpecificationFactory;
use PHPUnit_Framework_TestCase;

/**
 * @coversDefaultClass phpDocumentor\ApiReference\DocumentGroupDefinitionFactory
 */
final class DocumentGroupDefinitionFactoryTest extends PHPUnit_Framework_TestCase
{

    /**
     * @var DocumentGroupDefinitionFactory
     */
    private $fixture;

    /**
     * @var m\MockInterface
     */
    private $fileSystemFactoryMock;

    /**
     * @var m\MockInterface
     */
    private $specificationFactoryMock;

    protected function setUp()
    {
        $this->fileSystemFactoryMock = m::mock(FileSystemFactory::class);
        $this->specificationFactoryMock = m::mock(SpecificationFactory::class);
        $this->fixture = new DocumentGroupDefinitionFactory(
            $this->fileSystemFactoryMock,
            $this->specificationFactoryMock
        );
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $this->fileSystemFactoryMock->shouldReceive('create')
            ->andReturn(m::mock(FileSystem::class));
        $this->specificationFactoryMock->shouldReceive('create')
            ->with(['src'], [], [])
            ->andReturn(m::mock(SpecificationInterface::class));

        $documentGroupDefinition = $this->fixture->create(
            [
                'format' => 'php',
                'source' => [
                    'dsn' => 'file://.',
                    'paths' => [
                        'src',
                    ],
                ],
                'ignore' => [],
                'extensions' => []
            ]
        );

        $this->assertEquals(new DocumentGroupFormat('php'), $documentGroupDefinition->getFormat());
    }
}
