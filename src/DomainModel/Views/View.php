<?php

namespace phpDocumentor\DomainModel\Views;

final class View
{
    /** @var ViewDefinition */
    private $definition;

    /** @var mixed */
    private $data;

    public function __construct(ViewDefinition $definition, $data)
    {
        $this->definition = $definition;
        $this->data       = $data;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->definition->getName();
    }

    /**
     * @return mixed
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return $this->data;
    }
}
