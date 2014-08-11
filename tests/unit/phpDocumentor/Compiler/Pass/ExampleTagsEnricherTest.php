<?php

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;
use phpDocumentor\Descriptor\Example\Finder;

/**
 * Tests the \phpDocumentor\Compiler\Pass\ExampleTagsEnricher class.
 */
class ExampleTagsEnricherTest extends \PHPUnit_Framework_TestCase
{
    /** @var Finder|m\MockInterface */
    private $finderMock;

    /** @var ExampleTagsEnricher */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp()
    {
        $this->finderMock = m::mock('phpDocumentor\Descriptor\Example\Finder');
        $this->fixture    = new ExampleTagsEnricher($this->finderMock);
    }
    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::getDescription
     */
    public function testDescriptionName()
    {
        $this->assertSame('Enriches inline example tags with their sources', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagReturnsDescriptionIfItContainsNoExampleTags()
    {
        $description = 'This is a description';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $description);

        $project = $this->givenAProjectDescriptorWithChildDescriptors(array($descriptor));

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagWithExampleContents()
    {
        $exampleText = 'Example Text';
        $description = 'This is a description with {@example example2.txt} without description.';
        $expected    = "This is a description with `$exampleText` without description.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContains($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors(array($descriptor));

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagWithExampleContentsAndDescription()
    {
        $exampleText = 'Example Text';
        $description = 'This is a description with {@example example.txt including description}.';
        $expected    = "This is a description with *including description*`$exampleText`.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContains($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors(array($descriptor));

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplacingOfDescriptionHappensOncePerExample()
    {
        $exampleText = 'Example Text';
        $description = 'This is a description with {@example example.txt} and {@example example.txt}.';
        $expected    = "This is a description with `$exampleText` and `$exampleText`.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContainsAndMustBeCalledOnlyOnce($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors(array($descriptor));

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * Returns a mocked Descriptor with its description set to the given value.
     *
     * @param string $description
     *
     * @return m\MockInterface
     */
    private function givenAChildDescriptorWithDescription($description)
    {
        $descriptor = m::mock('phpDocumentor\Descriptor\DescriptorAbstract');
        $descriptor->shouldReceive('getDescription')->andReturn($description);

        return $descriptor;
    }

    /**
     * Returns a mocked Project Descriptor.
     *
     * @param m\MockInterface[] $descriptors
     *
     * @return m\MockInterface
     */
    private function givenAProjectDescriptorWithChildDescriptors($descriptors)
    {
        $projectDescriptor = m::mock('phpDocumentor\Descriptor\ProjectDescriptor');
        $projectDescriptor->shouldReceive('getIndexes->get')->with('elements')->andReturn($descriptors);

        return $projectDescriptor;
    }

    /**
     * Verifies if the given descriptor's setDescription method is called with the given value.
     *
     * @param m\MockInterface $descriptor
     * @param string          $expected
     *
     * @return void
     */
    public function thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected)
    {
        $descriptor->shouldReceive('setDescription')->with($expected);
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested.
     *
     * @param string $exampleText
     *
     * @return void
     */
    private function whenExampleTxtFileContains($exampleText)
    {
        $this->finderMock->shouldReceive('find')->andReturn($exampleText);
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested and verifies that that is only
     * done once.
     *
     * @param string $exampleText
     *
     * @return void
     */
    private function whenExampleTxtFileContainsAndMustBeCalledOnlyOnce($exampleText)
    {
        $this->finderMock->shouldReceive('find')->once()->andReturn($exampleText);
    }
}
