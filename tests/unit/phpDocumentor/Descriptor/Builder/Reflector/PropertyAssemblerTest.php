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

use phpDocumentor\Descriptor\NullDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Description;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Property;
use phpDocumentor\Reflection\Php\Visibility;
use phpDocumentor\Reflection\Types\String_;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

final class PropertyAssemblerTest extends TestCase
{
    /** @var PropertyAssembler $fixture */
    protected $fixture;

    /** @var ProjectDescriptorBuilder|ObjectProphecy */
    protected $builderMock;

    /**
     * Creates a new fixture to test with.
     */
    protected function setUp() : void
    {
        $this->builderMock = $this->prophesize(ProjectDescriptorBuilder::class);
        $this->builderMock->buildDescriptor(Argument::any(), Argument::any())
                          ->shouldBeCalled()
                          ->willReturn(new NullDescriptor());

        $this->fixture = new PropertyAssembler();
        $this->fixture->setBuilder($this->builderMock->reveal());
    }

    /**
     * @covers \phpDocumentor\Descriptor\Builder\Reflector\PropertyAssembler::create
     */
    public function testCreatePropertyDescriptorFromReflector() : void
    {
        // Arrange
        $namespace = 'Namespace';
        $propertyName = 'property';

        $propertyReflectorMock = $this->givenAPropertyReflector(
            $namespace,
            $propertyName,
            $this->givenADocBlockObject(true)
        );

        // Act
        $descriptor = $this->fixture->create($propertyReflectorMock);

        // Assert
        $expectedFqsen = '\\' . $namespace . '::$' . $propertyName;
        $this->assertSame($expectedFqsen, (string) $descriptor->getFullyQualifiedStructuralElementName());
        $this->assertSame($propertyName, $descriptor->getName());
        $this->assertSame('\\' . $namespace, $descriptor->getNamespace());
        $this->assertSame('protected', $descriptor->getVisibility());
        $this->assertSame('string', (string) $descriptor->getType());
        $this->assertFalse($descriptor->isStatic());
    }

    /**
     * Creates a sample property reflector for the tests with the given data.
     */
    private function givenAPropertyReflector(
        string $namespace,
        string $propertyName,
        ?DocBlock $docBlockMock = null
    ) : Property {
        return new Property(
            new Fqsen('\\' . $namespace . '::$' . $propertyName),
            new Visibility(Visibility::PROTECTED_),
            $docBlockMock,
            null,
            false,
            null,
            new String_()
        );
    }

    /**
     * Generates a DocBlock object with applicable defaults for these tests.
     */
    private function givenADocBlockObject(bool $withTags) : DocBlock
    {
        $docBlockDescription = new Description('This is an example description');

        $tags = [];

        if ($withTags) {
            $tags[] = new DocBlock\Tags\Var_(
                'variableName',
                new String_(),
                new Description('Var description')
            );
        }

        return new DocBlock('This is a example description', $docBlockDescription, $tags);
    }
}
