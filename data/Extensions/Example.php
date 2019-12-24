<?php

declare(strict_types=1);

namespace phpDocumentor\Extensions;

use phpDocumentor\Descriptor\ProjectDescriptor;

final class Example
{
    public function __invoke(ProjectDescriptor $projectDescriptor)
    {
        echo 'Extension is executed';
    }
}
