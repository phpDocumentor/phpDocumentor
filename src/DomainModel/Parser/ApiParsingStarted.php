<?php

namespace phpDocumentor\DomainModel\Parser;

use phpDocumentor\DomainModel\DomainEvent;
use phpDocumentor\DomainModel\Parser\Documentation\Api\Definition as ApiDefinition;

final class ApiParsingStarted extends DomainEvent
{
    /**
     * @var Definition
     */
    private $definition;

    /**
     * @param ApiDefinition $definition
     */
    public function __construct(ApiDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function definition()
    {
        return $this->definition;
    }
}
