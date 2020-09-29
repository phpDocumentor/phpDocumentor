<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Pass;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use Mockery\MockInterface;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

/**
 * Tests the \phpDocumentor\Compiler\Pass\ExampleTagsEnricher class.
 */
class ExampleTagsEnricherTest extends MockeryTestCase
{
    /** @var Finder|m\MockInterface */
    private $finderMock;

    /** @var ExampleTagsEnricher */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp() : void
    {
        $this->finderMock = m::mock(ExampleFinder::class);
        $this->fixture    = new ExampleTagsEnricher($this->finderMock);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::getDescription
     */
    public function testDescriptionName() : void
    {
        $this->assertSame('Enriches inline example tags with their sources', $this->fixture->getDescription());
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagReturnsDescriptionIfItContainsNoExampleTags() : void
    {
        $description = new Description('This is a description');

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $description);

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor]);

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagWithExampleContents() : void
    {
        $description = new Description(
            'This is a description with %$1 without description.',
            [
                new Example('example2.txt')
            ]
        );

        $exampleText = 'Example Text';
        $expected    = "This is a description with `${exampleText}` without description.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContains($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor]);

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplaceExampleTagWithExampleContentsAndDescription() : void
    {
        $exampleText = 'Example Text';
        $description = 'This is a description with {@example example.txt including description}.';
        $expected    = "This is a description with *including description*`${exampleText}`.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContains($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor]);

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::__construct
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::execute
     * @covers \phpDocumentor\Compiler\Pass\ExampleTagsEnricher::replaceInlineExamples
     */
    public function testReplacingOfDescriptionHappensOncePerExample() : void
    {
        $exampleText = 'Example Text';
        $description = 'This is a description with {@example example.txt} and {@example example.txt}.';
        $expected    = "This is a description with `${exampleText}` and `${exampleText}`.";

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenExampleTxtFileContainsAndMustBeCalledOnlyOnce($exampleText);
        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor]);

        $this->fixture->execute($project);

        $this->assertTrue(true);
    }

    /**
     * Returns a mocked Descriptor with its description set to the given value.
     */
    private function givenAChildDescriptorWithDescription(Description $description) : MockInterface
    {
        $descriptor = m::mock(DescriptorAbstract::class);
        $descriptor->shouldReceive('getDescription')->andReturn($description);

        return $descriptor;
    }

    /**
     * Returns a mocked Project Descriptor.
     *
     * @param m\MockInterface[] $descriptors
     */
    private function givenAProjectDescriptorWithChildDescriptors($descriptors) : MockInterface
    {
        $projectDescriptor = m::mock(ProjectDescriptor::class);
        $projectDescriptor->shouldReceive('getIndexes->get')->with('elements')->andReturn($descriptors);

        return $projectDescriptor;
    }

    /**
     * Verifies if the given descriptor's setDescription method is called with the given value.
     */
    public function thenDescriptionOfDescriptorIsChangedInto(m\MockInterface $descriptor, Description $expected) : void
    {
        $descriptor->shouldReceive('setDescription')->with($expected);
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested.
     */
    private function whenExampleTxtFileContains(string $exampleText) : void
    {
        $this->finderMock->shouldReceive('find')->andReturn($exampleText);
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested and verifies that that is only
     * done once.
     */
    private function whenExampleTxtFileContainsAndMustBeCalledOnlyOnce(string $exampleText) : void
    {
        $this->finderMock->shouldReceive('find')->once()->andReturn($exampleText);
    }
}
