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
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\ApiSetDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\Php\File;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function md5;

/**
 * Test class for \phpDocumentor\Descriptor\Builder
 *
 * @covers \phpDocumentor\Descriptor\Builder\Reflector\FileAssembler
 */
final class FileAssemblerTest extends TestCase
{
    use ProphecyTrait;

    /** @var FileAssembler $fixture */
    private $fixture;

    /** @var PackageDescriptor */
    private $defaultPackage;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp(): void
    {
        $this->defaultPackage = new PackageDescriptor();
        $this->defaultPackage->setName('\\PhpDocumentor');
        $this->fixture = new FileAssembler();
        $this->fixture->setBuilder($this->getApiSetDescriptorBuilderMock()->reveal());
    }

    /**
     * Creates a Descriptor from a provided class.
     */
    public function testCreateFileDescriptorFromReflector(): void
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

        self::assertSame($filename, $descriptor->getName());
        self::assertSame($hash, $descriptor->getHash());
        self::assertSame($content, $descriptor->getSource());
        //TODO: check this when we are testing default package behavior
        //$this->assertSame($this->defaultPackage, $descriptor->getPackage());
    }

    /**
     * Create a descriptor builder mock
     */
    protected function getApiSetDescriptorBuilderMock(): ObjectProphecy
    {
        $ApiSetDescriptorBuilderMock = $this->prophesize(ApiSetDescriptorBuilder::class);
        $ApiSetDescriptorBuilderMock->getDefaultPackage()->shouldBeCalled()->willReturn($this->defaultPackage);
        $ApiSetDescriptorBuilderMock->shouldIncludeSource()->shouldBeCalled()->willReturn(true);

        $ApiSetDescriptorBuilderMock->buildDescriptor(Argument::any(), Argument::any())->will(function () {
            $mock = $this->prophesize(DescriptorAbstract::class);
            $mock->setLocation(Argument::any())->shouldBeCalled();
            $mock->getTags()->shouldBeCalled()->willReturn(new Collection());
            $mock->getFullyQualifiedStructuralElementName()->shouldBeCalledOnce()->willReturn('Frank_is_een_eindbaas');

            return $mock->reveal();
        });

        return $ApiSetDescriptorBuilderMock;
    }
}
