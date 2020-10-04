<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

final class TestSubjectDescriptor extends DescriptorAbstract
{
    public function setParent(TestSubjectDescriptor $descriptor)
    {
        $this->inheritedElement = $descriptor;
    }
}
