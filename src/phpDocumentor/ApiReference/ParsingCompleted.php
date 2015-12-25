<?php

namespace phpDocumentor\ApiReference;

use League\Event\AbstractEvent;

final class ParsingCompleted extends AbstractEvent
{
    /**
     * @var DocumentGroupDefinition
     */
    private $definition;

    /**
     * @param DocumentGroupDefinition $definition
     */
    public function __construct(DocumentGroupDefinition $definition)
    {
        $this->definition = $definition;
    }

    public function definition()
    {
        return $this->definition;
    }
}
