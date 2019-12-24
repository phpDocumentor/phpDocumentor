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
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

final class PathGeneratorTest extends TestCase
{
    /** @var ObjectProphecy|Router */
    private $router;

    /** @var PathGenerator */
    private $generator;

    /** @var Template */
    private $template;

    /** @var vfsStreamDirectory */
    private $templatesFolder;

    /** @var vfsStreamDirectory */
    private $sourceFolder;

    /** @var vfsStreamDirectory */
    private $destinationFolder;

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

        $this->router = $this->prophesize(Router::class);
        $pathfinder = new Pathfinder();
        $this->generator = new PathGenerator(
            $this->router->reveal(),
            $pathfinder
        );
    }

    public function testGenerateAPathForTheGivenDescriptor() : void
    {
        $this->markTestIncomplete();

        $transformation = new Transformation(
            $this->template,
            '',
            'twig',
            'templates/templateName/index.html.twig',
            'index.html'
        );

        $this->generator->generate(new FileDescriptor('hash'), $transformation);
    }
}
