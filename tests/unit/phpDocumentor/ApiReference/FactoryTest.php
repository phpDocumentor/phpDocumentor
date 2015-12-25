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
use League\Event\Emitter;
use League\Flysystem\FilesystemInterface;
use Mockery as m;
use phpDocumentor\DocumentGroupDefinition as DocumentGroupDefinitionInterface;
use phpDocumentor\DocumentGroupFormat;

/**
 * @coversDefaultClass phpDocumentor\ApiReference\Factory
 */
class FactoryTest extends \PHPUnit_Framework_TestCase
{
    private $emitter;
    /**
     * @var Factory
     */
    private $fixture;

    protected function setUp()
    {
        $this->emitter = m::mock(Emitter::class);

        // TODO: Check explicitly whether the right events are emitted
        $this->emitter->shouldReceive('emit');
        $this->fixture = new Factory($this->emitter);
    }

    /**
     * @covers ::matches
     */
    public function testMatches()
    {
        $this->assertFalse($this->fixture->matches(m::mock(DocumentGroupDefinitionInterface::class)));
        $this->assertTrue($this->fixture->matches(
            new DocumentGroupDefinition(
                new DocumentGroupFormat('php'),
                m::mock(FilesystemInterface::class),
                m::mock(SpecificationInterface::class)
            )
        ));
    }

    /**
     * @covers ::create
     */
    public function testCreate()
    {
        $fileSystem = m::mock(FilesystemInterface::class);
        $fileSystem->shouldReceive('find')->andReturn([]);
        $format = new DocumentGroupFormat('php');
        $definition = new DocumentGroupDefinition($format, $fileSystem, m::mock(SpecificationInterface::class));

        $api = $this->fixture->create($definition);

        $this->assertSame($format, $api->getFormat());
    }
}
