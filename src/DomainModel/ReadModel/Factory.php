<?php

namespace phpDocumentor\DomainModel\ReadModel;

class Factory implements Mapper
{
    /** @var Factory */
    private $mapperFactory;

    public function __construct(Mapper\Factory $mapperFactory)
    {
        $this->mapperFactory = $mapperFactory;
    }

    /**
     * @inheritDoc
     */
    public function create(Definition $readModelDefinition, $documentation)
    {
        $mapper = $this->mapperFactory->create($readModelDefinition->getType());
        $data = $mapper->create($readModelDefinition, $documentation);

        foreach ($readModelDefinition->getFilters() as $filter) {
            $data = $filter($data);
        }

        return new ReadModel($readModelDefinition, $data);
    }
}
