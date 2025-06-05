<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Builder\Reflector\Reducer;

use phpDocumentor\Descriptor\Builder\AssemblerReducer;
use phpDocumentor\Descriptor\ConstantDescriptor;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\MethodDescriptor;
use phpDocumentor\Descriptor\PropertyDescriptor;
use phpDocumentor\Descriptor\ValueObjects\Visibility;
use phpDocumentor\Descriptor\ValueObjects\VisibilityModifier;
use phpDocumentor\Reflection\Php\AsymmetricVisibility;

class VisibilityReducer implements AssemblerReducer
{
    public function create(object $data, Descriptor|null $descriptor = null): Descriptor|null
    {
        if ($descriptor === null) {
            return null;
        }

        if (
            $descriptor instanceof MethodDescriptor === false &&
            $descriptor instanceof PropertyDescriptor === false &&
            $descriptor instanceof ConstantDescriptor === false
        ) {
            return $descriptor;
        }

        $descriptor->setVisibility($this->visibilityFromData($data));

        return $descriptor;
    }

    private function visibilityFromData(object $data): Visibility
    {
        if ($data->getVisibility() === null) {
            return new Visibility(VisibilityModifier::PUBLIC);
        }

        if ($data->getVisibility() instanceof AsymmetricVisibility) {
            return new Visibility(
                VisibilityModifier::from((string) $data->getVisibility()->getReadVisibility()),
                VisibilityModifier::from((string) $data->getVisibility()->getWriteVisibility()),
            );
        }

        return new Visibility(
            VisibilityModifier::from((string) $data->getVisibility()),
        );
    }
}
