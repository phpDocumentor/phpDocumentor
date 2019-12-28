<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Linker;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Reflection\Fqsen;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Linker\DescriptorRepository
 * @covers ::<private>
 */
final class DescriptorRepositoryTest extends MockeryTestCase
{
    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectAliasWithFqsenWhenContextIsClass() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyMethod()';
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');
        $container = m::mock(ClassDescriptor::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor\MyClass'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectAliasWithFqsenAndContainerWhenContextIsContainerNamespace() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\phpDocumentor\Descriptor\MyClass';
        $container = m::mock(DescriptorAbstract::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectAliasWithFqsenAndContainerWhenContextIsGlobalNamespace() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\MyClass';
        $container = m::mock(DescriptorAbstract::class);
        $container->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([$fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::findAlias
     */
    public function testFindObjectAliasReturnsNamespaceContextWhenElementIsUndocumented() : void
    {
        $fqsenWithContextMarker = '@context::MyClass';
        $container = m::mock(NamespaceDescriptor::class);
        $container
            ->shouldReceive('getFullyQualifiedStructuralElementName')
            ->andReturn(new Fqsen('\phpDocumentor\Descriptor'));
        $container->shouldReceive('getNamespace')->andReturn('\phpDocumentor\Descriptor');

        $linker = new DescriptorRepository();

        $this->assertSame(
            '\phpDocumentor\Descriptor\MyClass',
            (string) $linker->findAlias($fqsenWithContextMarker, $container)
        );
    }

    /**
     * @covers ::findAlias
     */
    public function testFindObjectAliasReturnsNothingWithUnknownFqsen() : void
    {
        $linker = new DescriptorRepository();

        $this->assertNull($linker->findAlias('\phpDocumentor\MyClass'));
    }
}
