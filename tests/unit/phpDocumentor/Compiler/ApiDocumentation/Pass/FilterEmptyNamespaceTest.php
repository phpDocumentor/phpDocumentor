<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use PHPUnit\Framework\TestCase;

final class FilterEmptyNamespaceTest extends TestCase
{
    use Faker;

    public function testEmptyNamespaceIsRemovedFromTree(): void
    {
        $parentNamespace = new NamespaceDescriptor();
        $parentNamespace->setName('Parent');
        $parentNamespace->setFullyQualifiedStructuralElementName(new Fqsen('\Parent'));

        $childNamespace = new NamespaceDescriptor();
        $childNamespace->setName('Child');
        $childNamespace->setFullyQualifiedStructuralElementName(new Fqsen('\Parent\Child'));
        $parentNamespace->addChild($childNamespace);
        $parentNamespace->getClasses()->set('Class1', self::faker()->classDescriptor(new Fqsen('\Parent\Class1')));

        $apiSet = self::faker()->apiSetDescriptor();
        $apiSet->setNamespace($parentNamespace);

        $fixture = new FilterEmptyNamespaces();
        $fixture($apiSet);

        $this->assertCount(0, $apiSet->getNamespace()->getChildren());
    }
}
