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

use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\ClassDescriptor;
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
    public function testFindObjectUsingWithFqsen() : void
    {
        $object = new ClassDescriptor();
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass');

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $linker->findAlias((string) $fqsen));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingPseudoTypes() : void
    {
        $object = new ClassDescriptor();
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor\MyClass'));

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $linker->findAlias('self::MyMethod()', $container));
        $this->assertSame($object, $linker->findAlias('$this::MyMethod()', $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingFqsenWithContextMarker() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyMethod()';
        $fqsen = new Fqsen('\phpDocumentor\Descriptor\MyClass::MyMethod()');

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor\MyClass'));
        $container->setNamespace('\phpDocumentor\Descriptor');

        $linker = new DescriptorRepository();
        $linker->setObjectAliasesList([(string) $fqsen => $object]);

        $this->assertSame($object, $linker->findAlias($fqsenWithContextMarker, $container));
    }

    /**
     * @covers ::setObjectAliasesList
     * @covers ::findAlias
     */
    public function testFindObjectUsingFqsenWhenContextRepresentsTheNamespace() : void
    {
        $object = new ClassDescriptor();
        $fqsenWithContextMarker = '@context::MyClass';
        $fqsen = '\phpDocumentor\Descriptor\MyClass';

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor\Descriptor');

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

        $container = new ClassDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor\Descriptor');

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
        $container = new NamespaceDescriptor();
        $container->setFullyQualifiedStructuralElementName(new Fqsen('\phpDocumentor\Descriptor'));
        $container->setNamespace('\phpDocumentor');

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
