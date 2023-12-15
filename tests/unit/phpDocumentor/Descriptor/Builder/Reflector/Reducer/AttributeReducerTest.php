<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Reducer;

use phpDocumentor\Descriptor\AttributeDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Attribute;
use phpDocumentor\Reflection\Php\Class_;
use PHPUnit\Framework\TestCase;
use stdClass;

final class AttributeReducerTest extends TestCase
{
    public function testDescriptorIsReturnedWhenNotAttributed(): void
    {
        $reducer = new AttributeReducer();

        $descriptor = $this->createMock(Descriptor::class);

        $this->assertSame(
            $descriptor,
            $reducer->create(
                new Class_(new Fqsen('\\MyClass')),
                $descriptor,
            ),
        );
    }

    public function testDescriptorIsReturnedWhenNotAttributeContainer(): void
    {
        $reducer = new AttributeReducer();
        $descriptor = new ClassDescriptor();

        $this->assertSame(
            $descriptor,
            $reducer->create(
                new stdClass(),
                $descriptor,
            ),
        );
    }

    public function testAttributeDescriptorIsAdded(): void
    {
        $reducer = new AttributeReducer();
        $descriptor = new ClassDescriptor();
        $exprectedAttribute = new AttributeDescriptor();
        $exprectedAttribute->setName('MyAttribute');
        $exprectedAttribute->setFullyQualifiedStructuralElementName(new Fqsen('\\MyAttribute'));

        $class = new Class_(new Fqsen('\\MyClass'));
        $class->addAttribute(new Attribute(new Fqsen('\\MyAttribute'), []));

        $result = $reducer->create($class, $descriptor);

        $this->assertEquals($exprectedAttribute, $result->getAttributes()[0]);
    }
}
