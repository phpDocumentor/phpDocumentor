<?php

declare(strict_types=1);

namespace phpDocumentor\FlowService;

use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Transformer\Template;

/**
 * Core class responsible for transforming the cache file to a set of artifacts.
 */
interface Transformer
{
    /**
     * Transforms the given project into a series of artifacts as provided by the template.
     */
    public function execute(ProjectDescriptor $project, DocumentationSetDescriptor $documentationSet, Template $template) : void;
}
