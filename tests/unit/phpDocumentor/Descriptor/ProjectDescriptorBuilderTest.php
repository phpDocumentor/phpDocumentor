<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor;

use \Mockery as m;
use phpDocumentor\Descriptor\ProjectDescriptor\Settings;
use phpDocumentor\Descriptor\Validator\Error;
use phpDocumentor\Plugin\Standards\Rule;
use Psr\Log\LogLevel;
use Symfony\Component\Validator\ConstraintViolation;

/**
 * Tests the functionality for the ProjectDescriptorBuilder class.
 */
class ProjectDescriptorBuilderTest extends \PHPUnit_Framework_TestCase
{
    /** @var m\MockInterface */
    private $validatorMock;

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
        $this->validatorMock = m::mock('Symfony\Component\Validator\Validator');

        $this->fixture = new ProjectDescriptorBuilder($this->assemblerFactory, $filterMock, $this->validatorMock);
    }

    /**
     * Demonstrates the basic usage the the ProjectDescriptorBuilder.
     *
     * This test scenario demonstrates how a ProjectDescriptorBuilder can be used to create a new ProjectDescriptor
     * and populate it with a single FileDescriptor using a FileReflector as source.
     *
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::createProjectDescriptor
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::buildFileUsingSourceData
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::getProjectDescriptor
     *
     * @see self::setUp on how to create an instance of the builder.
     *
     * @return void
     */
    public function testCreateNewProjectDescriptorAndBuildFile()
    {
        $this->markTestIncomplete('Finish later, in a hurry now.');
        // we use a FileReflector as example input
        $data = $this->createFileReflectorMock();

        $this->createFileDescriptorCreationMock();

        // usage example, see the setup how to instantiate the builder.
        $this->fixture->createProjectDescriptor();
        $this->fixture->buildFileUsingSourceData($data);
        $projectDescriptor = $this->fixture->getProjectDescriptor();

        // assert functioning
        $this->assertInstanceOf('phpDocumentor\Descriptor\ProjectDescriptor', $projectDescriptor);
        $this->assertCount(1, $projectDescriptor->getFiles());
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
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::setRuleset
     */
    public function testSettingARuleset()
    {
        $rulesetMock = m::mock('phpDocumentor\Plugin\Standards\Ruleset');

        $this->fixture->setRuleset($rulesetMock);

        $this->assertAttributeSame($rulesetMock, 'ruleset', $this->fixture);
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
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::validate
     */
    public function testValidatingADescriptorWithoutErrors()
    {
        $descriptorMock = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $violations = array();
        $this->validatorMock->shouldReceive('validate')->once()->with($descriptorMock)->andReturn($violations);

        $errors = $this->fixture->validate($descriptorMock);

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $errors);
        $this->assertCount(0, $errors->getAll());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::validate
     */
    public function testValidatingADescriptorWithErrorsButWithoutRuleset()
    {
        $fqsen = 'FQSEN';
        $line       = 1;
        $parameters = array('parameter');
        $message    = 'template';

        $descriptorMock = $this->givenDescriptorWithFqsenAndLinenumber($fqsen, $line);
        $this->thenDescriptorShouldBeValidatedAndAViolationIsEncountered(
            $descriptorMock,
            $this->givenAViolationWithMessageAndParameters($message, $parameters)
        );

        $errors = $this->fixture->validate($descriptorMock);

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $errors);
        $this->assertCount(1, $errors->getAll());

        /** @var Error $error */
        $error = $errors[0];
        $this->assertSame($message, $error->getCode());
        $this->assertSame($line, $error->getLine());
        $this->assertSame(LogLevel::ERROR, $error->getSeverity());
        $this->assertSame($parameters, $error->getContext());
    }

    /**
     * @covers phpDocumentor\Descriptor\ProjectDescriptorBuilder::validate
     */
    public function testValidatingADescriptorWithErrorsWithRuleset()
    {
        $message     = 'template';
        $ruleMessage = 'message';

        $ruleMock = $this->givenARuleWithMessageAndSeverity($ruleMessage, Rule::SEVERITY_ALERT);
        $descriptorMock = $this->givenDescriptorWithFqsenAndLinenumber('FQSEN', 1);
        $this->thenDescriptorShouldBeValidatedAndAViolationIsEncountered(
            $descriptorMock,
            $this->givenAViolationWithMessageAndParameters($message, array('parameter'))
        );
        $this->fixture->setRuleset($this->givenARulesetWithRuleAndRuleName($ruleMock, $message));

        $errors = $this->fixture->validate($descriptorMock);

        $this->assertInstanceOf('phpDocumentor\Descriptor\Collection', $errors);
        $this->assertCount(1, $errors->getAll());

        /** @var Error $error */
        $error = $errors[0];
        $this->assertSame($ruleMessage, $error->getCode());
        $this->assertSame(LogLevel::ALERT, $error->getSeverity());
    }

    /**
     * Creates a new FileReflector mock that can be used as input for the builder.
     *
     * @return m\MockInterface|\phpDocumentor\Reflection\FileReflector
     */
    protected function createFileReflectorMock()
    {
        return m::mock('phpDocumentor\Reflection\FileReflector');
    }

    /**
     * Creates a mocked FileDescriptor and register it so that it is returned by a mocked Assembler.
     *
     * @return void
     */
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

    /**
     * @param $fqsen
     * @param $line
     * @return m\MockInterface
     */
    private function givenDescriptorWithFqsenAndLinenumber($fqsen, $line)
    {
        $descriptorMock = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptorMock->shouldReceive('getFullyQualifiedStructuralElementName')->andReturn($fqsen);
        $descriptorMock->shouldReceive('getLine')->andReturn($line);
        return $descriptorMock;
    }

    /**
     * @param $message
     * @param $parameters
     * @return ConstraintViolation
     */
    private function givenAViolationWithMessageAndParameters($message, $parameters)
    {
        $violation = new ConstraintViolation('', $message, $parameters, null, '', '');
        return $violation;
    }

    /**
     * @param $descriptorMock
     * @param $violation
     */
    private function thenDescriptorShouldBeValidatedAndAViolationIsEncountered($descriptorMock, $violation)
    {
        $this->validatorMock->shouldReceive('validate')->once()->with($descriptorMock)->andReturn(array($violation));
    }

    /**
     * @param $ruleMessage
     * @param $ruleSeverity
     * @return m\MockInterface
     */
    private function givenARuleWithMessageAndSeverity($ruleMessage, $ruleSeverity)
    {
        return new Rule('ref', $ruleMessage, $ruleSeverity);
    }

    /**
     * @param $ruleMock
     * @param $message
     * @return m\MockInterface
     */
    private function givenARulesetWithRuleAndRuleName($ruleMock, $message)
    {
        $rulesetMock = m::mock('phpDocumentor\Plugin\Standards\Ruleset');
        $rulesetMock->shouldReceive('getRule')->with($message)->andReturn($ruleMock);
        return $rulesetMock;
    }
}
