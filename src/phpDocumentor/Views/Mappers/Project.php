<?php

namespace phpDocumentor\Views\Mappers;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Views\Mapper;
use phpDocumentor\Views\ViewDefinition;

class Project implements Mapper
{
    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param ViewDefinition $viewDefinition
     * @param                $documentation
     *
     * @return mixed
     */
    public function create(ViewDefinition $viewDefinition, $documentation)
    {
        if ($documentation instanceof ProjectDescriptor) {
            return $documentation;
        }

        throw new \InvalidArgumentException('The Project view type does not yet support the new v3 architecture');
    }
}
