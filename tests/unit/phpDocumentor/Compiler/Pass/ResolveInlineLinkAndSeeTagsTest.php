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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\Linker\DescriptorRepository;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\DescriptionFactory;
use phpDocumentor\Reflection\DocBlock\StandardTagFactory;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\FqsenResolver;
use phpDocumentor\Reflection\TypeResolver;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Pass\ResolveInlineLinkAndSeeTags
 * @covers ::__construct
 * @covers ::<private>
 */
final class ResolveInlineLinkAndSeeTagsTest extends TestCase
{
    /** @var Router|ObjectProphecy */
    private $router;

    /** @var ResolveInlineLinkAndSeeTags */
    private $fixture;

    /**
     * Initializes the fixture and its dependencies.
     */
    protected function setUp() : void
    {
        $this->router = $this->prophesize(Router::class);

        $fqsen = new Fqsen('\phpDocumentor\LinkDescriptor');
        $object = new ClassDescriptor();
        $object->setFullyQualifiedStructuralElementName($fqsen);
        $object->setNamespace('\phpDocumentor');

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([(string) $fqsen => $object]);

        $tagFactory = new StandardTagFactory(new FqsenResolver());
        $tagFactory->addService(new TypeResolver(new FqsenResolver()));
        $tagFactory->addService(new DescriptionFactory($tagFactory));

        $this->fixture = new ResolveInlineLinkAndSeeTags(
            $this->router->reveal(),
            $repository,
            $tagFactory
        );
    }

    /**
     * @covers ::getDescription
     */
    public function testDescriptionName() : void
    {
        $this->assertSame('Resolve @link and @see tags in descriptions', $this->fixture->getDescription());
    }

    /**
     * @covers ::execute
     */
    public function testDescriptionsWithoutTagsAreUnchanged() : void
    {
        $description = 'This is a description';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $description);
    }

    /**
     * @covers ::execute
     */
    public function testDescriptionsWithASeeTagWithALinkRendersAMarkdownLink() : void
    {
        $description = 'This is a {@see http://example.com description}';
        $expected = 'This is a [description](http://example.com)';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * @covers ::execute
     */
    public function testDescriptionsWithALinkTagRendersAMarkdownLink() : void
    {
        $description = 'This is a {@link http://example.com description}';
        $expected = 'This is a [description](http://example.com)';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);
        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * @covers ::execute
     */
    public function testTagsOtherThanSeeOrLinkAreNotAffected() : void
    {
        $description = 'Description with {@author John Doe}';
        $expected = 'Description with {@author John Doe}';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsASeeTagButFqsenIsNotInProject() : void
    {
        $description = 'Description with {@see ARandomDescriptor}';
        $expected = 'Description with \phpDocumentor\ARandomDescriptor';

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $collection = $this->givenACollection($descriptor);

        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfItContainsASeeTagAndFqsenIsInProject() : void
    {
        $description = 'Description with {@see \phpDocumentor\LinkDescriptor}';
        $expected = 'Description with [\phpDocumentor\LinkDescriptor](../classes/phpDocumentor.LinkDescriptor.html)';
        $elementToLinkTo = $this->givenAnElementToLinkTo();

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenDescriptionContainsSeeOrLinkWithElement($descriptor, $elementToLinkTo);

        $collection = $this->givenACollection($descriptor);
        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * @covers ::execute
     */
    public function testReplaceDescriptionIfFqsenIsAnAlias() : void
    {
        $description = 'Description with {@see LinkDescriptor}';
        $expected = 'Description with [\phpDocumentor\LinkDescriptor](../classes/phpDocumentor.LinkDescriptor.html)';
        $elementToLinkTo = $this->givenAnElementToLinkTo();

        $descriptor = $this->givenAChildDescriptorWithDescription($description);
        $this->whenDescriptionContainsSeeOrLinkWithElement($descriptor, $elementToLinkTo);

        $collection = $this->givenACollection($descriptor);
        $project = $this->givenAProjectDescriptorWithChildDescriptors($collection);

        $this->fixture->execute($project->reveal());

        $this->thenDescriptionOfDescriptorIsChangedInto($descriptor, $expected);
    }

    /**
     * Returns a mocked Descriptor with its description set to the given value.
     */
    private function givenAChildDescriptorWithDescription(string $description) : FileDescriptor
    {
        $descriptor = new FileDescriptor('7ft6ds57');
        $descriptor->setDescription($description);
        $descriptor->setNamespace('phpDocumentor');

        return $descriptor;
    }

    /**
     * Returns a mocked Project Descriptor.
     *
     * @param Collection|ObjectProphecy $descriptors
     */
    private function givenAProjectDescriptorWithChildDescriptors($descriptors) : ObjectProphecy
    {
        $projectDescriptor = $this->prophesize(ProjectDescriptor::class);
        $projectDescriptor->getIndexes()->shouldBeCalled()->willReturn($descriptors);

        return $projectDescriptor;
    }

    /**
     * Returns the descriptor of the element that the link points to
     */
    private function givenAnElementToLinkTo() : FileDescriptor
    {
        $elementToLinkTo = new FileDescriptor('sda');
        $elementToLinkTo->setNamespaceAliases(new Collection());

        return $elementToLinkTo;
    }

    /**
     * Returns a collection with descriptor. This collection will be scanned to see if a link can be made to a file.
     *
     * @param DescriptorAbstract|ObjectProphecy $descriptor
     *
     * @return Collection|ObjectProphecy
     */
    private function givenACollection($descriptor)
    {
        $collection = $this->prophesize(Collection::class);

        $items = ['\phpDocumentor\LinkDescriptor' => $descriptor];

        $collection->get(Argument::any())->shouldBeCalledOnce()->willReturn($items);

        return $collection;
    }

    /**
     * Verifies if the given descriptor's setDescription method is called with the given value.
     */
    private function thenDescriptionOfDescriptorIsChangedInto(FileDescriptor $descriptor, string $expected) : void
    {
        $this->assertSame($expected, $descriptor->getDescription());
    }

    /**
     * It resolves the element that is linked to
     */
    private function whenDescriptionContainsSeeOrLinkWithElement(
        FileDescriptor $descriptor,
        FileDescriptor $elementToLinkTo
    ) : FileDescriptor {
        $this->router->generate(Argument::any())->shouldBeCalled()->willReturn('/classes/phpDocumentor.LinkDescriptor.html');
        $descriptor->setFile($elementToLinkTo);

        return $descriptor;
    }
}
