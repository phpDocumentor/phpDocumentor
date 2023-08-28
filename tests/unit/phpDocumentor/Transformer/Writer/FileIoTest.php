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

use InvalidArgumentException;
use League\Flysystem\Adapter\Local;
use League\Flysystem\FileNotFoundException;
use League\Flysystem\Filesystem;
use League\Flysystem\MountManager;
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;
use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\FileIo
 * @covers \phpDocumentor\Transformer\Writer\IoTrait
 * @covers ::<private>
 */
final class FileIoTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    private vfsStreamDirectory $templatesFolder;
    private vfsStreamDirectory $sourceFolder;
    private vfsStreamDirectory $destinationFolder;
    private Template $template;

    private Transformer $transformer;

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
            ],
        );
        $this->template = new Template('My Template', $mountManager);
        $this->transformer = new Transformer(
            new Collection([]),
            new NullLogger(),
            $this->prophesize(EventDispatcherInterface::class)->reveal(),
        );
        $this->transformer->setDestination(new Filesystem(new Local($this->destinationFolder->url())));
    }

    /** @covers ::transform */
    public function testCopiesFileFromCustomTemplateToDestination(): void
    {
        $this->sourceFolder->addChild(new vfsStreamFile('index.html.twig'));

        $writer = new FileIo();

        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);

        $this->assertFalse($this->destinationFolder->hasChild('index.html'));
        $writer->transform(
            (new Transformation(
                $this->template,
                'copy',
                'fileio',
                'index.html.twig',
                'index.html',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('index.html'));
    }

    /** @covers ::transform */
    public function testCopiesFileFromGlobalTemplateToDestination(): void
    {
        $this->templatesFolder->addChild(new vfsStreamFile('templateName/images/image.png'));

        $writer = new FileIo();

        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);

        $this->assertFalse($this->destinationFolder->hasChild('images/destination.png'));
        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'templates/templateName/images/image.png',
                'images/destination.png',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images/destination.png'));
    }

    /** @covers ::transform */
    public function testCopiedFileOverwritesExistingFile(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);
        $this->sourceFolder->addChild(vfsStream::newFile('index.html.twig')->withContent('new content'));
        $this->destinationFolder->addChild(vfsStream::newFile('index.html')->withContent('original content'));

        $writer = new FileIo();

        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'index.html.twig',
                'index.html',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('index.html'));
        $this->assertStringEqualsFile($this->destinationFolder->getChild('index.html')->url(), 'new content');
    }

    /** @covers ::transform */
    public function testCopiesDirectoryFromCustomTemplateToDestination(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild(new vfsStreamFile('image1.png'));
        $this->sourceFolder->addChild($sourceDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'images',
                'images',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/image1.png'));
    }

    /** @covers ::transform */
    public function testCopiesDirectoryFromGlobalTemplateToDestination(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild(new vfsStreamFile('image1.png'));
        $templateDirectory = new vfsStreamDirectory('templateName');
        $templateDirectory->addChild($sourceDirectory);
        $this->templatesFolder->addChild($templateDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'templates/templateName/images',
                'images',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/image1.png'));
    }

    /** @covers ::transform */
    public function testCopiesDirectoryRecursivelyFromCustomTemplateToDestination(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);
        $subfolder = new vfsStreamDirectory('subfolder');
        $subfolder->addChild(new vfsStreamFile('image2.png'));
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild($subfolder);
        $this->sourceFolder->addChild($sourceDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'images',
                'images',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/subfolder/image2.png'));
    }

    /** @covers ::transform */
    public function testCopiesDirectoryRecursivelyFromGlobalTemplateToDestination(): void
    {
        $apiSet = $this->faker()->apiSetDescriptor();
        $project = $this->faker()->projectDescriptor([$this->faker()->versionDescriptor([$apiSet])]);
        $subfolder = new vfsStreamDirectory('subfolder');
        $subfolder->addChild(new vfsStreamFile('image2.png'));
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild($subfolder);
        $templateDirectory = new vfsStreamDirectory('templateName');
        $templateDirectory->addChild($sourceDirectory);
        $this->templatesFolder->addChild($templateDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            (
            new Transformation(
                $this->template,
                'copy',
                'fileio',
                'templates/templateName/images',
                'images',
            ))->setTransformer($this->transformer),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/subfolder/image2.png'));
    }

    /** @covers ::transform */
    public function testExceptionOccursIfSourceFileCannotBeFound(): void
    {
        $this->expectException(FileNotFoundException::class);
        $writer = new FileIo();

        $transformation = new Transformation(
            $this->template,
            'copy',
            'fileio',
            'unknown_file',
            'nah.png',
        );
        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->faker()->projectDescriptor(
            [$this->faker()->versionDescriptor([$apiSetDescriptor])],
        );

        $writer->transform($transformation, $projectDescriptor, $apiSetDescriptor);
    }

    /** @covers ::transform */
    public function testExceptionOccursIfQueryIsInvalid(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $writer = new FileIo();

        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->faker()->projectDescriptor(
            [$this->faker()->versionDescriptor([$apiSetDescriptor])],
        );
        $transformation = (new Transformation(
            $this->template,
            'not-a-copy',
            'fileio',
            'unknown_file',
            'nah.png',
        ))->setTransformer($this->transformer);

        $writer->transform($transformation, $projectDescriptor, $apiSetDescriptor);
    }
}
