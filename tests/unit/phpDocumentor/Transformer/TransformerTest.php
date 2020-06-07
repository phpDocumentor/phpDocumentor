<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Writer\Collection;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use Psr\Log\NullLogger;
use function strlen;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Transformer
 * @covers ::__construct
 * @covers ::<private>
 */
final class TransformerTest extends MockeryTestCase
{
    use Faker;

    /** @var int Max length of description printed. */
    private const MAX_DESCRIPTION_LENGTH = 68;

    /** @var Transformer $fixture */
    private $fixture = null;

    /** @var m\LegacyMockInterface|m\MockInterface|Template\Collection */
    private $templateCollectionMock;

    /** @var m\LegacyMockInterface|m\MockInterface|Collection */
    private $writerCollectionMock;

    /** @var m\LegacyMockInterface|m\MockInterface|FlySystemFactory */
    private $flySystemFactory;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     */
    protected function setUp() : void
    {
        $this->templateCollectionMock = m::mock(Template\Collection::class);
        $this->templateCollectionMock->shouldIgnoreMissing();
        $this->writerCollectionMock = m::mock(Collection::class);
        $this->writerCollectionMock->shouldIgnoreMissing();
        $this->flySystemFactory = m::mock(FlySystemFactory::class);
        $this->flySystemFactory->shouldReceive('create')->andReturn($this->faker()->fileSystem());

        $this->fixture = new Transformer(
            $this->templateCollectionMock,
            $this->writerCollectionMock,
            new NullLogger(),
            $this->flySystemFactory
        );
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization() : void
    {
        $templateCollectionMock = m::mock(Template\Collection::class);
        $templateCollectionMock->shouldIgnoreMissing();
        $writerCollectionMock = m::mock(Collection::class);
        $writerCollectionMock->shouldIgnoreMissing();
        $flySystemFactory = m::mock(FlySystemFactory::class);

        $fixture = new Transformer(
            $templateCollectionMock,
            $writerCollectionMock,
            new NullLogger(),
            $flySystemFactory
        );

        $this->assertSame($templateCollectionMock, $fixture->getTemplates());
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     * @covers ::destination
     */
    public function testSettingAndGettingATarget() : void
    {
        $filesystem = $this->faker()->fileSystem();
        $this->flySystemFactory->shouldReceive('create')->andReturn($filesystem);

        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(__DIR__);

        $this->assertEquals(__DIR__, $this->fixture->getTarget());
        $this->assertEquals($filesystem, $this->fixture->destination());
    }

    /**
     * @covers ::getTemplates
     */
    public function testRetrieveTemplateCollection() : void
    {
        $this->assertEquals($this->templateCollectionMock, $this->fixture->getTemplates());
    }

    /**
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $myTestWriter = 'myTestWriter';

        $templateCollection = m::mock(Template\Collection::class);

        $project = m::mock(ProjectDescriptor::class);

        $myTestWriterMock = m::mock(WriterAbstract::class)
            ->shouldReceive('transform')->getMock();

        $writerCollectionMock = m::mock(Collection::class)
            ->shouldReceive('offsetGet')
            ->with($myTestWriter)
            ->andReturn($myTestWriterMock)
            ->getMock();

        $fixture = new Transformer(
            $templateCollection,
            $writerCollectionMock,
            new NullLogger(),
            $this->flySystemFactory
        );

        $transformation = m::mock(Transformation::class)
            ->shouldReceive('execute')->with($project)
            ->shouldReceive('getQuery')->andReturn('')
            ->shouldReceive('getWriter')->andReturn($myTestWriter)
            ->shouldReceive('getArtifact')->andReturn('')
            ->shouldReceive('setTransformer')->with($fixture)
            ->getMock();

        $templateCollection->shouldReceive('getTransformations')->andReturn(
            [$transformation]
        );

        $fixture->execute($project);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription() : void
    {
        $description = $this->fixture->getDescription();
        $this->assertNotNull($description);
        $this->assertLessThanOrEqual(self::MAX_DESCRIPTION_LENGTH, strlen($description));
    }
}
