<?php

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\ProjectDescriptor;

interface Initializable
{
    public function initialize(ProjectDescriptor $projectDescriptor);
}
