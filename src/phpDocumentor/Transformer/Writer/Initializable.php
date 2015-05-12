<?php

namespace phpDocumentor\Transformer\Writer;

use phpDocumentor\Descriptor\Interfaces\ProjectInterface;

interface Initializable
{
    public function initialize(ProjectInterface $projectDescriptor);
}
