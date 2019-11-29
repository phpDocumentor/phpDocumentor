<?php

declare(strict_types=1);

namespace phpDocumentor\Application\Stage;

use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Transformer\Template\Collection;
use phpDocumentor\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use const DIRECTORY_SEPARATOR;
use function getcwd;

/**
 * @coversDefaultClass \phpDocumentor\Application\Stage\Transform
 * @covers ::__construct()
 */
final class TransformTest extends TestCase
{
    private $projectDescriptorBuilder;
    private $transformer;
    private $compiler;
    private $logger;
    private $exampleFinder;
    private $transform;

    public function setUp() : void
    {
        $this->projectDescriptorBuilder = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->transformer              = $this->prophesize(Transformer::class);
        $this->compiler                 = new Compiler();
        $this->logger                   = $this->prophesize(LoggerInterface::class);
        $this->exampleFinder            = $this->prophesize(ExampleFinder::class);

        $this->transform = new Transform(
            $this->transformer->reveal(),
            $this->compiler,
            $this->logger->reveal(),
            $this->exampleFinder->reveal()
        );
    }

    /**
     * @covers ::__invoke()
     * @covers ::setTargetLocationBasedOnDsn()
     */
    public function test_if_target_location_for_output_is_set_with_a_relative_path() : void
    {
        $config  = $this->givenAnExampleConfigWithDsnAndTemplates('file://.');
        $payload = new Payload($config, $this->projectDescriptorBuilder->reveal());

        $this->transformer->setTarget(getcwd() . DIRECTORY_SEPARATOR . '.')->shouldBeCalled();

        ($this->transform)($payload);
    }

    /**
     * @covers ::__invoke()
     * @covers ::setTargetLocationBasedOnDsn()
     */
    public function test_if_target_location_for_output_is_set_with_an_absolute_path() : void
    {
        $config  = $this->givenAnExampleConfigWithDsnAndTemplates('file:///my/absolute/folder');
        $payload = new Payload($config, $this->projectDescriptorBuilder->reveal());

        $this->transformer->setTarget('/my/absolute/folder')->shouldBeCalled();

        ($this->transform)($payload);
    }

    /**
     * @covers ::__invoke()
     * @covers ::loadTemplatesBasedOnNames()
     */
    public function test_loading_templates_with_a_given_set_of_template_names() : void
    {
        $config  = $this->givenAnExampleConfigWithDsnAndTemplates(
            'file://.',
            [
                ['name' => 'template1'],
                ['name' => 'template2'],
            ]
        );
        $payload = new Payload($config, $this->projectDescriptorBuilder->reveal());

        $this->transformer->setTarget(Argument::any());

        $templateCollection = $this->prophesize(Collection::class);
        $templateCollection->load('template1')->shouldBeCalled();
        $templateCollection->load('template2')->shouldBeCalled();

        $this->transformer->getTemplates()->willReturn($templateCollection->reveal());

        ($this->transform)($payload);
    }

    /**
     * @covers ::__invoke()
     * @covers ::doTransform()
     */
    public function test_transforming_the_project_will_invoke_all_compiler_passes() : void
    {
        $config            = $this->givenAnExampleConfigWithDsnAndTemplates('file://.');
        $payload           = new Payload($config, $this->projectDescriptorBuilder->reveal());
        $projectDescriptor = new ProjectDescriptor('my-project');
        $this->projectDescriptorBuilder->getProjectDescriptor()->willReturn($projectDescriptor);

        $this->transformer->setTarget(Argument::any());

        $compilerPass = $this->prophesize(CompilerPassInterface::class);
        $compilerPass->execute($projectDescriptor)->shouldBeCalled();

        $this->compiler->insert($compilerPass->reveal());

        ($this->transform)($payload);
    }

    private function givenAnExampleConfigWithDsnAndTemplates(string $dsn, array $templates = []) : array
    {
        return [
            'phpdocumentor' => [
                'paths' => [
                    'output' => new Dsn($dsn),
                ],
                'templates' => $templates,
            ],
        ];
    }
}
