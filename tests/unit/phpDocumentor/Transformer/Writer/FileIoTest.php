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
use org\bovigo\vfs\vfsStream;
use org\bovigo\vfs\vfsStreamDirectory;
use org\bovigo\vfs\vfsStreamFile;
use phpDocumentor\Faker\Faker;
use phpDocumentor\FileSystem\FlySystemAdapter;
use phpDocumentor\FileSystem\MountManager;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\FileIo
 * @covers \phpDocumentor\Transformer\Writer\IoTrait
 */
final class FileIoTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    private vfsStreamDirectory $templatesFolder;
    private vfsStreamDirectory $sourceFolder;
    private vfsStreamDirectory $destinationFolder;
    private Template $template;

    protected function setUp(): void
    {
        $root = vfsStream::setup();
        $this->templatesFolder = vfsStream::newDirectory('templates');
        $root->addChild($this->templatesFolder);
        $this->sourceFolder = vfsStream::newDirectory('source');
        $root->addChild($this->sourceFolder);
        $this->destinationFolder = vfsStream::newDirectory('destination');
        $root->addChild($this->destinationFolder);

        $this->transformer = $this->prophesize(Transformer::class);
        $this->transformer->destination()->willReturn(
            FlySystemAdapter::createFromFileSystem(
                new Filesystem(new Local($this->destinationFolder->url(), 0)),
            ),
        );

        $mountManager = new MountManager(
            [
                'templates' =>  FlySystemAdapter::createFromFileSystem(
                    new Filesystem(new Local($this->templatesFolder->url())),
                ),
                'template' => FlySystemAdapter::createFromFileSystem(
                    new Filesystem(new Local($this->sourceFolder->url())),
                ),
                'destination' => FlySystemAdapter::createFromFileSystem(
                    new Filesystem(new Local($this->destinationFolder->url(), 0)),
                ),
            ],
        );

        $this->template = new Template(
            'My Template',
            $mountManager,
        );
    }

    public function testCopiesFileFromCustomTemplateToDestination(): void
    {
        $this->sourceFolder->addChild(new vfsStreamFile('index.html.twig'));

        $writer = new FileIo();

        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);

        $this->assertFalse($this->destinationFolder->hasChild('index.html'));

        $transformation = $this->getTransformation('index.html.twig', 'index.html');

        $writer->transform($transformation, $project, $apiSet);
        $this->assertTrue($this->destinationFolder->hasChild('index.html'));
    }

    public function testCopiesFileFromGlobalTemplateToDestination(): void
    {
        $this->templatesFolder->addChild(new vfsStreamFile('templateName/images/image.png'));

        $writer = new FileIo();

        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);

        $writer->transform(
            $this->getTransformation(
                'templates/templateName/images/image.png',
                'images/destination.png',
            ),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images/destination.png'));
    }

    public function testCopiedFileOverwritesExistingFile(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);
        $this->sourceFolder->addChild(
            vfsStream::newFile('index.html.twig')->withContent('new content'),
        );
        $this->destinationFolder->addChild(
            vfsStream::newFile('index.html')->withContent('original content'),
        );

        $writer = new FileIo();

        $writer->transform(
            $this->getTransformation(
                'index.html.twig',
                'index.html',
            ),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('index.html'));
        $this->assertStringEqualsFile(
            $this->destinationFolder->getChild('index.html')->url(),
            'new content',
        );
    }

    public function testCopiesDirectoryFromCustomTemplateToDestination(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild(new vfsStreamFile('image1.png'));
        $this->sourceFolder->addChild($sourceDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            $this->getTransformation(
                'images',
                'images',
            ),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/image1.png'));
    }

    public function testCopiesDirectoryFromGlobalTemplateToDestination(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild(new vfsStreamFile('image1.png'));
        $templateDirectory = new vfsStreamDirectory('templateName');
        $templateDirectory->addChild($sourceDirectory);
        $this->templatesFolder->addChild($templateDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            $this->getTransformation(
                'templates/templateName/images',
                'images',
            ),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/image1.png'));
    }

    public function testCopiesDirectoryRecursivelyFromCustomTemplateToDestination(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);
        $subfolder = new vfsStreamDirectory('subfolder');
        $subfolder->addChild(new vfsStreamFile('image2.png'));
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild($subfolder);
        $this->sourceFolder->addChild($sourceDirectory);

        $writer = new FileIo();

        $this->assertFalse($this->destinationFolder->hasChild('images'));
        $writer->transform(
            $this->getTransformation(
                'images',
                'images',
            ),
            $project,
            $apiSet,
        );
        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/subfolder/image2.png'));
    }

    public function testCopiesDirectoryRecursivelyFromGlobalTemplateToDestination(): void
    {
        $apiSet = self::faker()->apiSetDescriptor();
        $project = self::faker()->projectDescriptor([self::faker()->versionDescriptor([$apiSet])]);
        $subfolder = new vfsStreamDirectory('subfolder');
        $subfolder->addChild(new vfsStreamFile('image2.png'));
        $sourceDirectory = new vfsStreamDirectory('images');
        $sourceDirectory->addChild($subfolder);
        $templateDirectory = new vfsStreamDirectory('templateName');
        $templateDirectory->addChild($sourceDirectory);
        $this->templatesFolder->addChild($templateDirectory);

        $writer = new FileIo();

        $writer->transform(
            $this->getTransformation(
                'templates/templateName/images',
                'images',
            ),
            $project,
            $apiSet,
        );

        $this->assertTrue($this->destinationFolder->hasChild('images'));
        $this->assertTrue($this->destinationFolder->hasChild('images/subfolder/image2.png'));
    }

    private function getTransformation(string $source, string $artifact): Transformation
    {
        $transformation = new Transformation(
            $this->template,
            'copy',
            'fileio',
            $source,
            $artifact,
        );
        $transformation->setTransformer($this->transformer->reveal());

        return $transformation;
    }
}
