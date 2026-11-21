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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Descriptor\Builder\AssemblerFactory;
use phpDocumentor\Descriptor\Filter\Filter;
use phpDocumentor\ProjectDescriptorBuilder;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * Tests the functionality for the ProjectDescriptorBuilder class.
 */
#[CoversClass(ProjectDescriptorBuilder::class)]
class ProjectDescriptorBuilderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ProjectDescriptorBuilder $fixture */
    protected $fixture;

    /**
     * Mock of the required AssemblerFactory dependency of the $fixture.
     *
     * @var AssemblerFactory|ObjectProphecy
     */
    protected $assemblerFactory;

    /**
     * Sets up a minimal fixture with mocked dependencies.
     */
    protected function setUp(): void
    {
        $this->assemblerFactory = $this->createAssemblerFactoryMock();
        $filterMock = $this->prophesize(Filter::class)->reveal();

        $this->fixture = new ProjectDescriptorBuilder($this->assemblerFactory->reveal(), $filterMock);
    }

    public function testCreatesAnEmptyProjectDescriptorWhenCalledFor(): void
    {
        $this->fixture->createProjectDescriptor();

        $this->assertInstanceOf(ProjectDescriptor::class, $this->fixture->getProjectDescriptor());
        $this->assertEquals(
            ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME,
            $this->fixture->getProjectDescriptor()->getName(),
        );
    }

    /**
     * Creates a new FileReflector mock that can be used as input for the builder.
     */
    protected function createFileReflectorMock(): object
    {
        return $this->prophesize('phpDocumentor\Reflection\FileReflector')->reveal();
    }

    protected function createFileDescriptorCreationMock(): void
    {
        $fileDescriptor = $this->prophesize(FileDescriptor::class);
        $fileDescriptor->setErrors(Argument::any())->willReturn(null);
        $fileDescriptor->getPath()->willReturn('abc');

        $fileAssembler = $this->prophesize('stdClass');
        $fileAssembler->setBuilder(Argument::cetera())->willReturn(null);
        $fileAssembler->create('phpDocumentor\Reflection\FileReflector')
            ->willReturn($fileDescriptor->reveal());

        $this->assemblerFactory->get('phpDocumentor\Reflection\FileReflector')
            ->willReturn($fileAssembler->reveal());
    }

    /**
     * Creates a Mock of an AssemblerFactory.
     *
     * When a FileReflector (or mock thereof) is passed to the 'get' method this mock will return an
     * empty instance of the FileDescriptor class.
     *
     * @return ObjectProphecy
     */
    protected function createAssemblerFactoryMock()
    {
        return $this->prophesize(AssemblerFactory::class);
    }

    public function testSetVisibility(): void
    {
        $this->fixture->createProjectDescriptor();
        $this->fixture->setVisibility(ApiSpecification::VISIBILITY_PUBLIC);
        $projectSettings = $this->fixture->getProjectDescriptor()->getSettings();

        self::assertEquals(ApiSpecification::VISIBILITY_PUBLIC, $projectSettings->getVisibility());
    }

    public function testSetIncludeSourceTogglesTheProjectSetting(): void
    {
        $this->fixture->createProjectDescriptor();
        $projectSettings = $this->fixture->getProjectDescriptor()->getSettings();

        self::assertFalse($projectSettings->shouldIncludeSource());

        $this->fixture->setIncludeSource(true);
        self::assertTrue($projectSettings->shouldIncludeSource());

        $this->fixture->setIncludeSource(false);
        self::assertFalse($projectSettings->shouldIncludeSource());
    }
}
