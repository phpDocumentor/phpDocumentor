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

namespace phpDocumentor\Transformer\Writer;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Query\Engine;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Twig\Environment;
use Twig\Loader\ArrayLoader;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig
 * @covers ::__construct
 * @covers ::<private>
 * @covers \phpDocumentor\Transformer\Writer\IoTrait
 * @covers \phpDocumentor\Transformer\Writer\WriterAbstract
 */
final class TwigTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /** @var vfsStreamDirectory */
    private $templatesFolder;

    /** @var vfsStreamDirectory */
    private $sourceFolder;

    /** @var vfsStreamDirectory */
    private $destinationFolder;

    /** @var Template */
    private $template;

    /** @var EnvironmentFactory|ObjectProphecy */
    private $environmentFactory;

    /** @var Twig */
    private $writer;

    /** @var PathGenerator&ObjectProphecy */
    private $pathGenerator;

    protected function setUp(): void
    {
        $root = vfsStream::setup();
        $this->templatesFolder = vfsStream::newDirectory('templates');
        $root->addChild($this->templatesFolder);
        $this->sourceFolder = vfsStream::newDirectory('source');
        $root->addChild($this->sourceFolder);
        $this->destinationFolder = vfsStream::newDirectory('destination');
        $root->addChild($this->destinationFolder);

        $mountManager = new MountManager(
            [
                'templates' => new Filesystem(new Local($this->templatesFolder->url())),
                'template' => new Filesystem(new Local($this->sourceFolder->url())),
                // VFS does not support locking, hence the 0
                'destination' => new Filesystem(new Local($this->destinationFolder->url(), 0)),
            ]
        );
        $this->template = new Template('My Template', $mountManager);

        $this->environmentFactory = $this->prophesize(EnvironmentFactory::class);
        $this->pathGenerator = $this->prophesize(PathGenerator::class);

        $this->writer = new Twig(
            $this->environmentFactory->reveal(),
            $this->pathGenerator->reveal(),
            $this->prophesize(Engine::class)->reveal()
        );
    }

    /**
     * @covers \phpDocumentor\Transformer\Writer\WriterAbstract::__toString
     */
    public function testReturnsClassNameAsDescription(): void
    {
        $this->assertSame(Twig::class, (string) $this->writer);
    }

    /**
     * @covers ::transform
     */
    public function testRendersTwigTemplateToDestination(): void
    {
        $targetDir = $this->destinationFolder->url();
        $transformer = $this->givenTransformerWithTarget($targetDir);
        $this->pathGenerator->generate(Argument::any(), Argument::any())->willReturn('index.html');

        $this->givenATwigEnvironmentFactoryWithTemplates(
            ['/index.html.twig' => 'This is a twig file']
        );

        $transformation = new Transformation(
            $this->template,
            '',
            'twig',
            'templates/templateName/index.html.twig',
            'index.html'
        );
        $transformation->setTransformer($transformer->reveal());

        $project = new ProjectDescriptor('project');
        $this->writer->initialize($project, $this->faker()->template());
        $this->writer->transform($project, $transformation);

        $this->assertFileExists($targetDir . '/index.html');
        $this->assertStringEqualsFile($targetDir . '/index.html', 'This is a twig file');
    }

    private function givenATwigEnvironmentFactoryWithTemplates(array $templates): void
    {
        $this->environmentFactory->create(Argument::cetera())->willReturn(
            new Environment(
                new ArrayLoader($templates)
            )
        );
    }

    private function givenTransformerWithTarget(string $targetDir): ObjectProphecy
    {
        $transformer = $this->prophesize(Transformer::class);
        $transformer->getTarget()->willReturn($targetDir);

        return $transformer;
    }
}
