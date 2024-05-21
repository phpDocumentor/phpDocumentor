<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Reducer;

use phpDocumentor\Descriptor\AttributeDescriptor;
use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Interfaces\AttributedInterface;
use phpDocumentor\Descriptor\ValueObjects\CallArgument;
use phpDocumentor\Reflection\Php\AttributeContainer;

final class AttributeReducer implements AssemblerReducer
{
    public function create(object $data, Descriptor|null $descriptor = null): Descriptor|null
    {
        if ($descriptor instanceof AttributedInterface === false) {
            return $descriptor;
        }

        if ($data instanceof AttributeContainer === false) {
            return $descriptor;
        }

        foreach ($data->getAttributes() as $attribute) {
            $attributeDescriptor = new AttributeDescriptor();
            $attributeDescriptor->setName($attribute->getName());
            $attributeDescriptor->setFullyQualifiedStructuralElementName($attribute->getFqsen());
            foreach ($attribute->getArguments() as $argument) {
                $attributeDescriptor->addArgument(new CallArgument($argument->getValue(), $argument->getName()));
            }

            $descriptor->addAttribute($attributeDescriptor);
        }

        return $descriptor;
    }
}
