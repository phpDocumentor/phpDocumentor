<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Nodes\DefinitionLists;

final class DefinitionList
{
    /** @var DefinitionListTerm[] */
    private $terms;

    /**
     * @param DefinitionListTerm[] $terms
     */
    public function __construct(array $terms)
    {
        $this->terms = $terms;
    }

    /**
     * @return DefinitionListTerm[]
     */
    public function getTerms(): array
    {
        return $this->terms;
    }
}
