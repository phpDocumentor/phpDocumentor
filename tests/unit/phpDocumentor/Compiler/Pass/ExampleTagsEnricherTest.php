<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

/**
 * Tests the \phpDocumentor\Compiler\Pass\ExampleTagsEnricher class.
 */
class ExampleTagsEnricherTest extends TestCase
{
    /** @var Finder|ObjectProphecy */
    private $finderMock;

    /** @var ExampleTagsEnricher */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp() : void
    {
        $this->finderMock = $this->prophesize(ExampleFinder::class);
        $this->fixture    = new ExampleTagsEnricher($this->finderMock->reveal());
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

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor->reveal()]);

        $this->fixture->execute($project->reveal());

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

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor->reveal()]);

        $this->fixture->execute($project->reveal());

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

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor->reveal()]);

        $this->fixture->execute($project->reveal());

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

        $project = $this->givenAProjectDescriptorWithChildDescriptors([$descriptor->reveal()]);

        $this->fixture->execute($project->reveal());

        $this->assertTrue(true);
    }

    /**
     * Returns a mocked Descriptor with its description set to the given value.
     */
    private function givenAChildDescriptorWithDescription(Description $description) : ObjectProphecy
    {
        $descriptor = $this->prophesize(DescriptorAbstract::class);
        $descriptor->getDescription()->shouldBeCalled()->willReturn($description);

        return $descriptor;
    }

    /**
     * Returns a mocked Project Descriptor.
     *
     * @param ObjectProphecy[] $descriptors
     */
    private function givenAProjectDescriptorWithChildDescriptors($descriptors) : ObjectProphecy
    {
        $collection = $this->prophesize(Collection::class);
        $collection->get(Argument::exact('elements'))->willReturn($descriptors);

        $projectDescriptor = $this->prophesize(ProjectDescriptor::class);
        $projectDescriptor->getIndexes()->shouldBeCalled()->willReturn($collection->reveal());

        return $projectDescriptor;
    }

    /**
     * Verifies if the given descriptor's setDescription method is called with the given value.
     */
    public function thenDescriptionOfDescriptorIsChangedInto(ObjectProphecy $descriptor, Description $expected) : void
    {
        $descriptor->setDescription(Argument::exact($expected))->shouldBeCalled();
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested.
     */
    private function whenExampleTxtFileContains(string $exampleText) : void
    {
        $this->finderMock->find(Argument::any())->shouldBeCalled()->willReturn($exampleText);
    }

    /**
     * Instructs the finder mock to return the given text when an example is requested and verifies that that is only
     * done once.
     */
    private function whenExampleTxtFileContainsAndMustBeCalledOnlyOnce(string $exampleText) : void
    {
        $this->finderMock->find(Argument::any())->shouldBeCalledOnce()->willReturn($exampleText);
    }
}
