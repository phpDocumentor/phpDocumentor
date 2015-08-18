<?php

namespace phpDocumentor\Views;

use phpDocumentor\Descriptor\ProjectDescriptor;

class ViewFactory implements Mapper
{
    /**
     * @var MapperFactory
     */
    private $mapperFactory;

    public function __construct(MapperFactory $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(ViewDefinition $viewDefinition, $documentation)
    {
        $mapper = $this->mapperFactory->create($viewDefinition->getType());
        $data = $mapper->create($viewDefinition, $documentation);

        foreach ($viewDefinition->getFilters() as $filter) {
            $data = $filter($data);
        }

        return new View($viewDefinition, $data);
    }
}
