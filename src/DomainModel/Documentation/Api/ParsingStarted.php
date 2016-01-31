<?php

namespace phpDocumentor\DomainModel\Documentation\Api;

use League\Event\AbstractEvent;
use phpDocumentor\DomainModel\Documentation\Api\Definition;

final class ParsingStarted extends AbstractEvent
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
