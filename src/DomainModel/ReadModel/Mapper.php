<?php

namespace phpDocumentor\DomainModel\ReadModel;

interface Mapper
{
    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param Definition $readModelDefinition
     * @param                $documentation
     *
     * @return mixed
     */
    public function create(Definition $readModelDefinition, $documentation);
}
