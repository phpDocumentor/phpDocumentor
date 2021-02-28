<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;

interface FlowService
{
    public function operate(DocumentationSetDescriptor $documentationSet): void;
}
