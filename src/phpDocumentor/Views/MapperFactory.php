<?php

namespace phpDocumentor\Views;

interface MapperFactory
{
    /**
     * Returns a mapper for the given type of view.
     *
     * @param ViewType $viewType
     *
     * @return Mapper
     */
    public function create(ViewType $viewType);
}
