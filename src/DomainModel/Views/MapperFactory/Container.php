<?php

namespace phpDocumentor\DomainModel\Views\MapperFactory;

use Interop\Container\ContainerInterface;
use phpDocumentor\DomainModel\Views\Mapper;
use phpDocumentor\DomainModel\Views\MapperFactory;
use phpDocumentor\DomainModel\Views\ViewType;

class Container implements MapperFactory
{
    /** @var ContainerInterface */
    private $container;

    /** @var string[] */
    private $mapperAliases;

    public function __construct(ContainerInterface $container, array $mapperAliases = [])
    {
        $this->container     = $container;
        $this->mapperAliases = $mapperAliases;
    }

    /**
     * Returns a mapper for the given type of view.
     *
     * @param ViewType $viewType
     *
     * @return Mapper
     */
    public function create(ViewType $viewType)
    {
        $mapperName = (string)$viewType;
        if (isset($this->mapperAliases[$mapperName])) {
            $mapperName = $this->mapperAliases[$mapperName];
        }

        return $this->container->get($mapperName);
    }
}
