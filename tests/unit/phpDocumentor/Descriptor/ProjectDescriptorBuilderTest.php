<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;

/**
 * Tests the functionality for the ProjectDescriptorBuilder class.
 */
class ProjectDescriptorBuilderTest extends \Mockery\Adapter\Phpunit\MockeryTestCase
{
    /** @var \phpDocumentor\Descriptor\ProjectDescriptorBuilder $fixture */
    protected $fixture;

    /**
     * Mock of the required AssemblerFactory dependency of the $fixture.
     *
     * @var \phpDocumentor\Descriptor\Builder\AssemblerFactory|m\MockInterface $assemblerFactory
     */
    protected $assemblerFactory;

    /**
     * Sets up a minimal fixture with mocked dependencies.
     */
    protected function setUp()
    {
        $this->assemblerFactory = $this->createAssemblerFactoryMock();
        $filterMock = m::mock('phpDocumentor\Descriptor\Filter\Filter');

        $this->fixture = new ProjectDescriptorBuilder($this->assemblerFactory, $filterMock);
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::createProjectDescriptor
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::getProjectDescriptor
     */
    public function testCreatesAnEmptyProjectDescriptorWhenCalledFor()
    {
        $this->fixture->createProjectDescriptor();

        $this->assertInstanceOf('phpDocumentor\Descriptor\ProjectDescriptor', $this->fixture->getProjectDescriptor());
        $this->assertEquals(
            ProjectDescriptorBuilder::DEFAULT_PROJECT_NAME,
            $this->fixture->getProjectDescriptor()->getName()
        );
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::setProjectDescriptor
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::getProjectDescriptor
     */
    public function testProvidingAPreExistingDescriptorToBuildOn()
    {
        $projectDescriptorName = 'My Descriptor';
        $projectDescriptorMock = new ProjectDescriptor($projectDescriptorName);
        $this->fixture->setProjectDescriptor($projectDescriptorMock);

        $this->assertSame($projectDescriptorMock, $this->fixture->getProjectDescriptor());
        $this->assertEquals($projectDescriptorName, $this->fixture->getProjectDescriptor()->getName());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::isVisibilityAllowed
     */
    public function testDeterminesWhetherASpecificVisibilityIsAllowedToBeIncluded()
    {
        $projectDescriptorName = 'My Descriptor';
        $projectDescriptorMock = new ProjectDescriptor($projectDescriptorName);
        $projectDescriptorMock->getSettings()->setVisibility(Settings::VISIBILITY_PUBLIC);
        $this->fixture->setProjectDescriptor($projectDescriptorMock);

        $this->assertTrue($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PUBLIC));
        $this->assertFalse($this->fixture->isVisibilityAllowed(Settings::VISIBILITY_PRIVATE));
    }

    /**
     * Creates a new FileReflector mock that can be used as input for the builder.
     */
    protected function createFileReflectorMock(): m\MockInterface
    {
        return m::mock('phpDocumentor\Reflection\FileReflector');
    }

    protected function createFileDescriptorCreationMock()
    {
        $fileDescriptor = m::mock('phpDocumentor\Descriptor\FileDescriptor');
        $fileDescriptor->shouldReceive('setErrors');
        $fileDescriptor->shouldReceive('getPath')->andReturn('abc');

        $fileAssembler = m::mock('stdClass');
        $fileAssembler->shouldReceive('setBuilder')->withAnyArgs();
        $fileAssembler->shouldReceive('create')
            ->with('phpDocumentor\Reflection\FileReflector')
            ->andReturn($fileDescriptor);

        $this->assemblerFactory->shouldReceive('get')
            ->with('phpDocumentor\Reflection\FileReflector')
            ->andReturn($fileAssembler);
    }

    /**
     * Creates a Mock of an AssemblerFactory.
     *
     * When a FileReflector (or mock thereof) is passed to the 'get' method this mock will return an
     * empty instance of the FileDescriptor class.
     *
     * @return m\MockInterface|\phpDocumentor\Descriptor\Builder\AssemblerFactory
     */
    protected function createAssemblerFactoryMock()
    {
        return m::mock('phpDocumentor\Descriptor\Builder\AssemblerFactory');
    }
}
