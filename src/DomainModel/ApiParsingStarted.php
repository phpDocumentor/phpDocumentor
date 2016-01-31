<?php

namespace phpDocumentor\DomainModel;

use League\Event\AbstractEvent;
use phpDocumentor\DomainModel\Documentation\Api\Definition;

final class ApiParsingStarted extends AbstractEvent
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
