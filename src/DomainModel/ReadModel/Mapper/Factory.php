<?php

namespace phpDocumentor\DomainModel\ReadModel\Mapper;

use phpDocumentor\DomainModel\ReadModel\Mapper;
use phpDocumentor\DomainModel\ReadModel\Type;

interface Factory
{
    /**
     * Returns a mapper for the given type of view.
     *
     * @param Type $viewType
     *
     * @return Mapper
     */
    public function create(Type $viewType);
}
