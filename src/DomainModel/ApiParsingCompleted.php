<?php

namespace phpDocumentor\DomainModel;

use phpDocumentor\DomainModel\DomainEvent;
use phpDocumentor\DomainModel\Documentation\Api\Definition;

final class ApiParsingCompleted extends DomainEvent
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     * @param Definition $definition
     */
    public function __construct(Definition $definition)
    {
        $this->definition = $definition;
    }

    public function definition()
    {
        return $this->definition;
    }
}
