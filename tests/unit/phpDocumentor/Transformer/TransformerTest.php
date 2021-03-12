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
use phpDocumentor\FileSystem\FlySystemFactory;
use phpDocumentor\Transformer\Writer\Collection;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
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
    use ProphecyTrait;
    use Faker;

    /** @var int Max length of description printed. */
    private const MAX_DESCRIPTION_LENGTH = 68;

    /** @var Transformer $fixture */
    private $fixture = null;

    /** @var ObjectProphecy|Collection */
    private $writerCollectionMock;

    /** @var ObjectProphecy|FlySystemFactory */
    private $flySystemFactory;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     */
    protected function setUp() : void
    {
        $this->writerCollectionMock = $this->prophesize(Collection::class);
        $this->flySystemFactory = $this->prophesize(FlySystemFactory::class);
        $this->flySystemFactory->create(Argument::any())->willReturn($this->faker()->fileSystem());

        $this->fixture = new Transformer(
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
        $writerCollectionMock = $this->prophesize(Collection::class);
        $flySystemFactory = $this->prophesize(FlySystemFactory::class);

        $fixture = new Transformer(
            $writerCollectionMock->reveal(),
            new NullLogger(),
            $flySystemFactory->reveal()
        );

        self::assertSame('Transform analyzed project into artifacts', $fixture->getDescription());
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
     * @covers ::execute
     */
    public function testExecute() : void
    {
        $myTestWriter = 'myTestWriter';
        $project = $this->prophesize(ProjectDescriptor::class);

        $myTestWriterMock = $this->prophesize(WriterAbstract::class);
        $myTestWriterMock->transform(Argument::any(), Argument::any())->shouldBeCalled();

        $this->writerCollectionMock->offsetGet($myTestWriter)
            ->shouldBeCalled()
            ->willReturn($myTestWriterMock->reveal());

        $transformation = $this->prophesize(Transformation::class);
        $transformation->getQuery()->shouldBeCalled()->willReturn('');
        $transformation->template()->willReturn($this->faker()->template());
        $transformation->getWriter()->shouldBeCalled()->willReturn($myTestWriter);
        $transformation->getArtifact()->shouldBeCalled()->willReturn('');
        $transformation->setTransformer(Argument::exact($this->fixture))->shouldBeCalled();

        $this->fixture->execute($project->reveal(), [$transformation->reveal()]);
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
