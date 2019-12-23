<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer;

use League\Flysystem\Adapter\Local;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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
    use Faker;

    /** @var vfsStreamDirectory */
    private $templatesFolder;

    /** @var vfsStreamDirectory */
    private $sourceFolder;

    /** @var vfsStreamDirectory */
    private $destinationFolder;

    /** @var Template */
    private $template;

    protected function setUp() : void
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
                'destination' => new Filesystem(new Local($this->destinationFolder->url())),
            ]
        );
        $this->template = new Template('My Template', $mountManager);
    }

    /**
     * @covers ::transform
     */
    public function testRendersTwigTemplateToDestination() : void
    {
        $root = vfsStream::setup();
        $targetDir = vfsStream::newDirectory('target')->at($root)->url();
        $transformer = $this->givenTransformerWithTarget($targetDir);

        $environmentFactory = $this->givenATwigEnvironmentFactoryWithTemplates(
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

        $writer = new Twig(
            $environmentFactory->reveal(),
            new PathGenerator($this->prophesize(Router::class)->reveal())
        );
        $writer->transform(new ProjectDescriptor('project'), $transformation);

        $this->assertFileExists($targetDir . '/index.html');
        $this->assertStringEqualsFile($targetDir . '/index.html', 'This is a twig file');
    }

    private function givenATwigEnvironmentFactoryWithTemplates(array $templates) : ObjectProphecy
    {
        $environmentFactory = $this->prophesize(EnvironmentFactory::class);
        $environmentFactory->create(Argument::cetera())->willReturn(
            new Environment(
                new ArrayLoader($templates)
            )
        );

        return $environmentFactory;
    }

    private function givenTransformerWithTarget(string $targetDir) : ObjectProphecy
    {
        $transformer = $this->prophesize(Transformer::class);
        $transformer->getTarget()->willReturn($targetDir);

        return $transformer;
    }
}
