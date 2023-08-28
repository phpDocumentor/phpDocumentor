<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use League\Flysystem\Adapter\NullAdapter;
use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use Mockery\LegacyMockInterface;
use Mockery\MockInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Template\Collection;
use phpDocumentor\Transformer\Template\Factory;
use phpDocumentor\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\ProphecyMock;
use Psr\Log\LoggerInterface;

/**
 * @coversDefaultClass \phpDocumentor\Pipeline\Stage\Transform
 * @covers ::__construct
 */
final class TransformTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /** @var ProjectDescriptorBuilder|ProphecyMock */
    private $projectDescriptorBuilder;

    /** @var Transformer|ProphecyMock */
    private $transformer;

    /** @var LoggerInterface|ProphecyMock */
    private $logger;

    /** @var Compile */
    private Transform $transform;

    /** @var LegacyMockInterface|MockInterface|FlySystemFactory */
    private $flySystemFactory;

    public function setUp(): void
    {
        $documentationSet = $this->faker()->apiSetDescriptor();
        $projectDescriptor = new ProjectDescriptor('test');
        $projectDescriptor->getVersions()->add($this->faker()->versionDescriptor([$documentationSet]));

        $this->flySystemFactory = $this->prophesize(FlySystemFactory::class);
        $this->flySystemFactory->create(Argument::type(Dsn::class))->willReturn(new Filesystem(new NullAdapter()));
        $this->projectDescriptorBuilder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->projectDescriptorBuilder->getProjectDescriptor()->willReturn($projectDescriptor);
        $this->transformer = $this->prophesize(Transformer::class);
        $this->logger = $this->prophesize(LoggerInterface::class);
        $this->transformer->execute($projectDescriptor, $documentationSet, [], Argument::type(FilesystemInterface::class))->shouldBeCalled();
        $templateFactory = $this->prophesize(Factory::class);
        $templateFactory->getTemplates(Argument::any(), Argument::any())->willReturn(new Collection());

        $this->transform = new Transform(
            $this->transformer->reveal(),
            $this->flySystemFactory->reveal(),
            $this->logger->reveal(),
            $templateFactory->reveal(),
        );
    }

    /**
     * @covers ::__invoke
     * @covers ::createFileSystem
     */
    public function test_if_target_location_for_output_is_set_with_a_relative_path(): void
    {
        $config = $this->givenAnExampleConfigWithDsnAndTemplates('.');
        $payload = new Payload($config, $this->projectDescriptorBuilder->reveal());

        ($this->transform)($payload);
    }

    /**
     * @covers ::__invoke
     * @covers ::createFileSystem
     */
    public function test_if_target_location_for_output_is_set_with_an_absolute_path(): void
    {
        $config = $this->givenAnExampleConfigWithDsnAndTemplates('file:///my/absolute/folder');
        $payload = new Payload($config, $this->projectDescriptorBuilder->reveal());

        ($this->transform)($payload);
    }

    private function givenAnExampleConfigWithDsnAndTemplates(string $dsn, array $templates = []): array
    {
        return [
            'phpdocumentor' => [
                'paths' => [
                    'output' => Dsn::createFromString($dsn),
                ],
                'templates' => $templates,
            ],
        ];
    }
}
