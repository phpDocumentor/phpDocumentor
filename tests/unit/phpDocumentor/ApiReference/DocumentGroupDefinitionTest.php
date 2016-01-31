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
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DomainModel\Documentation\Api\Definition;
use phpDocumentor\DomainModel\Documentation\DocumentGroup\DocumentGroupFormat;

/**
 * @coversDefaultClass \phpDocumentor\ApiReference\DocumentGroupDefinition
 * @covers ::__construct
 */
class DocumentGroupDefinitionTest extends \PHPUnit_Framework_TestCase
{
    /** @var  Definition */
    private $definition;

    /** @var SpecificationInterface|m\MockInterface */
    private $specification;

    /** @var FilesystemInterface|m\MockInterface */
    private $fileSystem;

    /** @var DocumentGroupFormat */
    private $format;

    public function setUp()
    {
        $this->fileSystem = m::mock(FileSystemInterface::class);
        $this->specification = m::mock(SpecificationInterface::class);
        $this->format = new DocumentGroupFormat('PHP');

        $this->definition = new Definition($this->format, $this->fileSystem, $this->specification);
    }

    /**
     * @covers ::getFiles
     */
    public function testFindingTheProjectFilesAndReturningThem()
    {
        $result = [['path' => 'myFile.php'], ['path' => 'someFile.php']];

        $this->fileSystem->shouldReceive('find')->with($this->specification)->andReturn(new \ArrayIterator($result));

        $this->assertSame('myFile.php', $this->definition->getFiles()[0]->path());
        $this->assertSame('someFile.php', $this->definition->getFiles()[1]->path());
    }

    /**
     * @covers ::getFormat
     */
    public function testExposeTheFormatForThisDefinition()
    {
        $this->assertSame($this->format, $this->definition->getFormat());
    }

    /**
     * @covers ::getFilesystem
     */
    public function testExposeTheFilesystemForThisDefinition()
    {
        $this->assertSame($this->fileSystem, $this->definition->getFilesystem());
    }
}
