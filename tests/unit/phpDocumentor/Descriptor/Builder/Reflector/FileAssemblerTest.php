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

namespace phpDocumentor\Descriptor\Builder\Reflector;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use function md5;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\FileAssembler
 */
final class FileAssemblerTest extends TestCase
{
    /** @var FileAssembler $fixture */
    private $fixture;

    /** @var PackageDescriptor */
    private $defaultPackage;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
    {
        $this->defaultPackage = new PackageDescriptor();
        $this->defaultPackage->setName('\\PhpDocumentor');
        $this->fixture = new FileAssembler();
        $this->fixture->setBuilder($this->getProjectDescriptorBuilderMock()->reveal());
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateFileDescriptorFromReflector() : void
    {
        $filename = 'file.php';
        $content = '<?php ... ?>';
        $hash = md5($content);

        $docBlockDescription = new DocBlock\Description(
            <<<DOCBLOCK
            /**
             * This is a example description
             */
DOCBLOCK
        );

        $docBlockMock = new DocBlock('This is a example description', $docBlockDescription);

        $fileReflectorMock = new File($hash, $filename, $content, $docBlockMock);

        $descriptor = $this->fixture->create($fileReflectorMock);

        $this->assertSame($filename, $descriptor->getName());
        $this->assertSame($hash, $descriptor->getHash());
        $this->assertSame($content, $descriptor->getSource());
        //TODO: check this when we are testing default package behavior
        //$this->assertSame($this->defaultPackage, $descriptor->getPackage());
    }

    /**
     * Create a descriptor builder mock
     */
    protected function getProjectDescriptorBuilderMock() : ObjectProphecy
    {
        $settings = new Settings();
        $settings->includeSource();

        $projectDescriptor = $this->prophesize(ProjectDescriptor::class);
        $projectDescriptor->getSettings()->shouldBeCalled()->willReturn($settings);

        $projectDescriptorBuilderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $projectDescriptorBuilderMock->getDefaultPackage()->shouldBeCalled()->willReturn($this->defaultPackage);
        $projectDescriptorBuilderMock
            ->getProjectDescriptor()
            ->shouldBeCalled()
            ->willReturn($projectDescriptor->reveal());

        $projectDescriptorBuilderMock->buildDescriptor(Argument::any(), Argument::any())->will(function () {
            $mock = $this->prophesize(DescriptorAbstract::class);
            $mock->setLocation(Argument::any())->shouldBeCalled();
            $mock->getTags()->shouldBeCalled()->willReturn(new Collection());
            $mock->getFullyQualifiedStructuralElementName()->shouldBeCalledOnce()->willReturn('Frank_is_een_eindbaas');

            return $mock->reveal();
        });

        return $projectDescriptorBuilderMock;
    }
}
