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

namespace phpDocumentor\Compiler\ApiDocumentation\Linker;

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\ApiDocumentation\Linker\DescriptorRepository
 * @covers ::<private>
 */
final class DescriptorRepositoryTest extends TestCase
{
    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingWithFqsen(): void
    {
        $object = new ClassDescriptor();
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass');

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $repository->findAlias((string) $fqsen));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingPseudoTypes(): void
    {
        $object = new ClassDescriptor();
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor\MyClass'));

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $repository->findAlias('self::MyMethod()', $container));
        $this->assertSame($object, $repository->findAlias('$this::MyMethod()', $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingFqsenWithContextMarker(): void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyMethod()';
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor\MyClass'));
        $container->setNamespace('\phpDocumentor\Descriptor');

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $repository->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingFqsenWhenContextRepresentsTheNamespace(): void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\phpDocumentor\Descriptor\MyClass';

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor\Descriptor');

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $repository->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectAliasWithFqsenAndContainerWhenContextIsGlobalNamespace(): void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\MyClass';

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor\Descriptor');

        $repository = new DescriptorRepository();
        $repository->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $repository->findAlias($fqsenWithContextMarker, $container));
    }

    /** @covers ::findAlias */
    public function testFindObjectAliasReturnsNamespaceContextWhenElementIsUndocumented(): void
    {
        $fqsenWithContextMarker = '@context::MyClass';
        $container = new NamespaceDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor');

        $repository = new DescriptorRepository();

        $this->assertSame(
            '\phpDocumentor\Descriptor\MyClass',
            (string) $repository->findAlias($fqsenWithContextMarker, $container),
        );
    }

    /** @covers ::findAlias */
    public function testFindObjectAliasReturnsNothingWithUnknownFqsen(): void
    {
        $repository = new DescriptorRepository();

        $this->assertNull($repository->findAlias('\phpDocumentor\MyClass'));
    }
}
