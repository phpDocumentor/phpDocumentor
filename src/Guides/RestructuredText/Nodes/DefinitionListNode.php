<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

use phpDocumentor\Guides\RestructuredText\Parser\DefinitionList;

class DefinitionListNode extends Node
{
    /** @var DefinitionList */
    private $definitionList;

    public function __construct(DefinitionList $definitionList)
    {
        parent::__construct();

        $this->definitionList = $definitionList;
    }

    public function getDefinitionList() : DefinitionList
    {
        return $this->definitionList;
    }
}
