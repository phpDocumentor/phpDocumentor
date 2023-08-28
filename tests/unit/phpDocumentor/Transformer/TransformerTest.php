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

use League\Flysystem\FilesystemInterface;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Writer\Collection;
use phpDocumentor\Transformer\Writer\WriterAbstract;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Psr\Log\NullLogger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

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

    private const MAX_DESCRIPTION_LENGTH = 68;

    private Transformer|null $fixture = null;

    /** @var WriterAbstract&ObjectProphecy */
    private $writer;

    /**
     * Instantiates a new \phpDocumentor\Transformer for use as fixture.
     */
    protected function setUp(): void
    {
        $this->writer = $this->prophesize(WriterAbstract::class);
        $this->writer->getName()->willReturn('myTestWriter');
        $this->writer->__toString()->willReturn('myTestWriter');
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $eventDispatcher->dispatch(Argument::any(), Argument::any())->willReturnArgument(0);

        $this->fixture = new Transformer(
            new Collection(['myTestWriter' => $this->writer->reveal()]),
            new NullLogger(),
            $eventDispatcher->reveal(),
        );
    }

    /** @covers ::__construct */
    public function testInitialization(): void
    {
        $fixture = new Transformer(
            new Collection([]),
            new NullLogger(),
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
        );

        self::assertSame('Transform analyzed project into artifacts', $fixture->getDescription());
    }

    /**
     * @covers ::getTarget
     * @covers ::setTarget
     */
    public function testSettingAndGettingATarget(): void
    {
        $this->assertEquals('', $this->fixture->getTarget());

        $this->fixture->setTarget(__DIR__);

        $this->assertEquals(__DIR__, $this->fixture->getTarget());
    }

    /** @covers ::execute */
    public function testExecute(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);

        $transformation = $this->prophesize(Transformation::class);
        $transformation->getQuery()->shouldBeCalled()->willReturn('');
        $transformation->template()->willReturn($this->faker()->template());
        $transformation->getWriter()->shouldBeCalled()->willReturn($this->writer);
        $transformation->getArtifact()->shouldBeCalled()->willReturn('');
        $transformation->setTransformer(Argument::exact($this->fixture))->shouldBeCalled();

        $this->writer->transform($transformation, $project, $apiSet)->shouldBeCalled();

        $this->fixture->execute(
            $project,
            $apiSet,
            [$transformation->reveal()],
            $this->prophesize(FilesystemInterface::class)->reveal(),
        );
    }

    /** @covers ::getDescription */
    public function testGetDescription(): void
    {
        $description = $this->fixture->getDescription();
        $this->assertNotNull($description);
        $this->assertLessThanOrEqual(self::MAX_DESCRIPTION_LENGTH, strlen($description));
    }
}
