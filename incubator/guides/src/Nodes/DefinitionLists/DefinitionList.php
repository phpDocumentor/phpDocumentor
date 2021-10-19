<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

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
