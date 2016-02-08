<?php

namespace phpDocumentor\Application\ReadModel\Mappers;

use phpDocumentor\DomainModel\ReadModel\Mapper;
use phpDocumentor\DomainModel\ReadModel\Definition;

class Project implements Mapper
{
    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param Definition $readModelDefinition
     * @param                $documentation
     *
     * @return mixed
     */
    public function create(Definition $readModelDefinition, $documentation)
    {
        if ($documentation instanceof ProjectDescriptor) {
            return $documentation;
        }

        throw new \InvalidArgumentException('The Project view type does not yet support the new v3 architecture');
    }
}
