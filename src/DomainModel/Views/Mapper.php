<?php

namespace phpDocumentor\DomainModel\Views;

interface Mapper
{
    /**
     * Returns the data needed by the ViewFactory to create a new View.
     *
     * @param ViewDefinition $viewDefinition
     * @param                $documentation
     *
     * @return mixed
     */
    public function create(ViewDefinition $viewDefinition, $documentation);
}
