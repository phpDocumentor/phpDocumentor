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

namespace phpDocumentor\Compiler\Linker;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function get_class;

/**
 * Tests the functionality for the Linker class.
 *
 * @coversDefaultClass \phpDocumentor\Compiler\Linker\Linker
 * @covers ::__construct
 * @covers ::<private>
 */
final class LinkerTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @var ObjectProphecy|DescriptorRepository */
    private $descriptorRepository;

    /** @var Linker */
    private $linker;

    protected function setUp(): void
    {
        $this->descriptorRepository = $this->prophesize(DescriptorRepository::class);
        $this->linker = new Linker([], $this->descriptorRepository->reveal());
    }

    /**
     * @covers ::getSubstitutions
     */
    public function testSetFieldsToSubstitute(): void
    {
        $elementList = [
            ProjectDescriptor::class => 'files',
            FileDescriptor::class    => 'classes',
            ClassDescriptor::class   => 'parent',
        ];
        $linker = new Linker($elementList, new DescriptorRepository());

        $this->assertSame($elementList, $linker->getSubstitutions());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteReturnsNullWhenPassingAnUnsupportedItemType(): void
    {
        $this->descriptorRepository->findAlias(Argument::cetera())->shouldNotBeCalled();

        // for example, integers cannot be substituted
        $result = $this->linker->substitute(1);

        $this->assertSame(null, $result);
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteReturnsDescriptorBasedOnFqsenString(): void
    {
        $fqsenString = '\My\Class';
        $container = null;

        $this->descriptorRepository->findAlias($fqsenString, $container)
            ->willReturn($this->givenAnExampleClassDescriptor($fqsenString));

        $result = $this->linker->substitute($fqsenString, $container);

        $this->assertInstanceOf(ClassDescriptor::class, $result);
        $this->assertSame($fqsenString, (string) $result->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteReturnsDescriptorBasedOnFqsenObject(): void
    {
        $fqsenString = '\My\Class';
        $container = null;
        $fqsen = new Fqsen($fqsenString);

        $this->descriptorRepository->findAlias($fqsenString, $container)
            ->willReturn($this->givenAnExampleClassDescriptor($fqsenString));

        $result = $this->linker->substitute($fqsen, $container);

        $this->assertInstanceOf(ClassDescriptor::class, $result);
        $this->assertSame($fqsenString, (string) $result->getFullyQualifiedStructuralElementName());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstituteReturnsNullIfFqsenCannotBeFound(): void
    {
        $container = null;
        $this->descriptorRepository->findAlias('\My\Class', $container)->willReturn(null);

        $result = $this->linker->substitute('\My\Class', $container);

        $this->assertNull($result);
    }

    /**
     * @covers ::substitute
     */
    public function testSubstitutingAnArrayReplacesAllElementsWithTheirDescriptors(): void
    {
        $fqsenString1 = '\My\Class1';
        $fqsenString2 = '\My\Class2';
        $fqsenString3 = '\My\Class3';
        $container = null;
        $class1 = $this->givenAnExampleClassDescriptor($fqsenString1);

        $this->descriptorRepository->findAlias($fqsenString1, $container)->willReturn($class1);
        $this->descriptorRepository->findAlias($fqsenString2, $container)->willReturn(null);
        $this->descriptorRepository->findAlias($fqsenString3, $container)->willReturn('\My\Class3');

        $result = $this->linker->substitute(
            [
                new Fqsen($fqsenString1), // Will be resolved to a ClassDescriptor
                new Fqsen($fqsenString2), // Won't be resolved and stays like this
                new Fqsen($fqsenString3), // Will be resolved to a string
            ],
            $container
        );

        $this->assertIsArray($result);
        $this->assertInstanceOf(ClassDescriptor::class, $result[0]);
        $this->assertInstanceOf(Fqsen::class, $result[1]);
        $this->assertIsString($result[2]);
        $this->assertSame($fqsenString1, (string) $result[0]->getFullyQualifiedStructuralElementName());
        $this->assertEquals(new Fqsen($fqsenString2), $result[1]);
        $this->assertSame($fqsenString3, $result[2]);
    }

    /**
     * @covers ::substitute
     */
    public function testSubstitutingAnArrayWorksRecursively(): void
    {
        $fqsenString1 = '\My\Class1';
        $fqsenString2 = '\My\Class2';
        $fqsenString3 = '\My\Class3';
        $container = null;
        $class1 = $this->givenAnExampleClassDescriptor($fqsenString1);

        $this->descriptorRepository->findAlias($fqsenString1, $container)->willReturn($class1);
        $this->descriptorRepository->findAlias($fqsenString2, $container)->willReturn(null);
        $this->descriptorRepository->findAlias($fqsenString3, $container)->willReturn('\My\Class3');

        $result = $this->linker->substitute(
            [
                [
                    new Fqsen($fqsenString1), // Will be resolved to a ClassDescriptor
                    [
                        new Fqsen($fqsenString2), // Won't be resolved and stays like this
                    ],
                ],
                new Fqsen($fqsenString3), // Will be resolved to a string
            ],
            $container
        );

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertCount(2, $result[0]);
        $this->assertCount(1, $result[0][1]);
        $this->assertInstanceOf(ClassDescriptor::class, $result[0][0]);
        $this->assertInstanceOf(Fqsen::class, $result[0][1][0]);
        $this->assertIsString($result[1]);
        $this->assertSame($fqsenString1, (string) $result[0][0]->getFullyQualifiedStructuralElementName());
        $this->assertEquals(new Fqsen($fqsenString2), $result[0][1][0]);
        $this->assertSame($fqsenString3, $result[1]);
    }

    /**
     * @covers ::substitute
     */
    public function testSubstitutingWillReplaceFieldsIndicatedInSubstitutionsProperty(): void
    {
        $this->linker = new Linker([ClassDescriptor::class => ['parent']], $this->descriptorRepository->reveal());

        $class = $this->givenAnExampleClassDescriptor('\My\Class');
        $parentFqsenString = '\My\Parent\Class';
        $parentFqsenObject = new Fqsen($parentFqsenString);
        $class->setParent($parentFqsenObject); // Set FQSEN that should be replaced

        $parentClass = $this->givenAnExampleClassDescriptor($parentFqsenString);

        $this->descriptorRepository->findAlias($parentFqsenString, $class)->willReturn($parentClass);

        $result = $this->linker->substitute($class);

        // Classes are modified and this method returns null to indicate that the calling location does not need
        // to be replaced.
        $this->assertNull($result);

        // The parent field value should be replaced with the given class
        $this->assertEquals($parentClass, $class->getParent());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstitutingWontReplaceFieldsWhenTheyReturnNull(): void
    {
        $class = $this->prophesize(ClassDescriptor::class);
        $class->getFullyQualifiedStructuralElementName()->willReturn(new Fqsen('\My\Class'));

        $this->linker = new Linker([get_class($class->reveal()) => ['parent']], $this->descriptorRepository->reveal());

        // Only when field returns null, no update happens
        $class->getParent()->willReturn(null)->shouldBeCalledOnce();
        $class->setParent(Argument::any())->shouldNotBeCalled();

        // include it twice; should only have its parent set once (see above)
        $this->linker->substitute($class->reveal());
    }

    /**
     * @covers ::substitute
     */
    public function testSubstitutingWillReplaceFieldsOnceForEachObject(): void
    {
        $class = $this->prophesize(ClassDescriptor::class);
        $class->getFullyQualifiedStructuralElementName()->willReturn(new Fqsen('\My\Class'));

        $this->linker = new Linker([get_class($class->reveal()) => ['parent']], $this->descriptorRepository->reveal());

        $parentFqsenString = '\My\Parent\Class';
        $parentFqsenObject = new Fqsen($parentFqsenString);

        $parentClass = $this->givenAnExampleClassDescriptor($parentFqsenString);

        // Only ONCE, even though it is present multiple times
        $class->getParent()->willReturn($parentFqsenObject)->shouldBeCalledOnce();
        $class->setParent($parentClass)->shouldBeCalledOnce();

        $this->descriptorRepository->findAlias($parentFqsenString, $class)->willReturn($parentClass);

        // include it twice; should only have its parent set once (see above)
        $this->linker->substitute([$class->reveal(), $class->reveal()]);
    }

    /**
     * @covers ::getDescription
     */
    public function testGetDescription(): void
    {
        $linker = new Linker([], new DescriptorRepository());
        $expected = 'Replace textual FQCNs with object aliases';
        $this->assertSame($expected, $linker->getDescription());
    }

    /**
     * @covers ::execute
     */
    public function testExecute(): void
    {
        $result = new ClassDescriptor();
        $object = $this->prophesize(ClassDescriptor::class);
        $fqsen = get_class($object);

        $project = $this->faker()->apiSetDescriptor();
        $project->getIndexes()->set('elements', new Collection([$fqsen => $result]));

        // prepare linker
        $repository = $this->prophesize(DescriptorRepository::class);
        $repository->setObjectAliasesList([$fqsen => $result])->shouldBeCalledOnce();
        $linker = new Linker([$fqsen => ['field']], $repository->reveal());

        // execute test.
        $linker->execute($project);
    }

    private function givenAnExampleClassDescriptor(string $fqsenString): ClassDescriptor
    {
        $exampleDescriptor = new ClassDescriptor();
        $exampleDescriptor->setFullyQualifiedStructuralElementName(new Fqsen($fqsenString));

        return $exampleDescriptor;
    }
}
