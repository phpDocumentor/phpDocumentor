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

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Writer\Collection;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use function strlen;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Transformer
 * @covers ::__construct
 * @covers ::<private>
 */
final class TransformerTest extends TestCase
{
    use \Prophecy\PhpUnit\ProphecyTrait;
    use Faker;

    /** @var int Max length of description printed. */
    private const MAX_DESCRIPTION_LENGTH = 68;

    /** @var Transformer $fixture */
    private $fixture = null;

    /** @var ObjectProphecy|Template\Collection */
    private $templateCollectionMock;

    /** @var ObjectProphecy|Collection */
    private $writerCollectionMock;

    /** @var ObjectProphecy|FlySystemFactory */
    private $flySystemFactory;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     */
    protected function setUp() : void
    {
        $this->templateCollectionMock = $this->prophesize(Template\Collection::class);
        $this->writerCollectionMock = $this->prophesize(Collection::class);
        $this->flySystemFactory = $this->prophesize(FlySystemFactory::class);
        $this->flySystemFactory->create(Argument::any())->willReturn($this->faker()->fileSystem());

        $this->fixture = new Transformer(
            $this->templateCollectionMock->reveal(),
            $this->writerCollectionMock->reveal(),
            new NullLogger(),
            $this->flySystemFactory->reveal()
        );
    }

    /**
     * @covers ::__construct
     */
    public function testInitialization() : void
    {
        $templateCollectionMock = $this->prophesize(Template\Collection::class);
        $writerCollectionMock = $this->prophesize(Collection::class);
        $flySystemFactory = $this->prophesize(FlySystemFactory::class);

        $fixture = new Transformer(
            $templateCollectionMock->reveal(),
            $writerCollectionMock->reveal(),
            new NullLogger(),
            $flySystemFactory->reveal()
        );

        $this->assertSame($templateCollectionMock->reveal(), $fixture->getTemplates());
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     * @covers ::destination
     */
    public function testSettingAndGettingATarget() : void
    {
        $filesystem = $this->faker()->fileSystem();
        $this->flySystemFactory->create(Argument::any())->willReturn($filesystem);

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
        $this->assertEquals($this->templateCollectionMock->reveal(), $this->fixture->getTemplates());
    }

    /**
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $myTestWriter = 'myTestWriter';

        $templateCollection = $this->prophesize(Template\Collection::class);

        $project = $this->prophesize(ProjectDescriptor::class);

        $myTestWriterMock = $this->prophesize(WriterAbstract::class);
        $myTestWriterMock->transform(Argument::any(), Argument::any())->shouldBeCalled();

        $writerCollectionMock = $this->prophesize(Collection::class);
        $writerCollectionMock->offsetGet($myTestWriter)
            ->shouldBeCalled()
            ->willReturn($myTestWriterMock->reveal());

        $fixture = new Transformer(
            $templateCollection->reveal(),
            $writerCollectionMock->reveal(),
            new NullLogger(),
            $this->flySystemFactory->reveal()
        );

        $transformation = $this->prophesize(Transformation::class);
        $transformation->getQuery()->shouldBeCalled()->willReturn('');
        $transformation->getWriter()->shouldBeCalled()->willReturn($myTestWriter);
        $transformation->getArtifact()->shouldBeCalled()->willReturn('');
        $transformation->setTransformer(Argument::exact($fixture))->shouldBeCalled();

        $templateCollection->getTransformations()
            ->shouldBeCalled()
            ->willReturn([$transformation->reveal()]);

        $fixture->execute($project->reveal());
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
