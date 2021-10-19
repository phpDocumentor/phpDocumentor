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

use phpDocumentor\Guides\Nodes\SpanNode;
use RuntimeException;

final class DefinitionListTerm
{
    /** @var SpanNode */
    private $term;

    /** @var SpanNode[] */
    private $classifiers;

    /** @var SpanNode[] */
    private $definitions;

    /**
     * @param SpanNode[] $classifiers
     * @param SpanNode[] $definitions
     */
    public function __construct(SpanNode $term, array $classifiers, array $definitions)
    {
        $this->term = $term;
        $this->classifiers = $classifiers;
        $this->definitions = $definitions;
    }

    public function getTerm(): SpanNode
    {
        return $this->term;
    }

    /**
     * @return SpanNode[]
     */
    public function getClassifiers(): array
    {
        return $this->classifiers;
    }

    /**
     * @return SpanNode[]
     */
    public function getDefinitions(): array
    {
        return $this->definitions;
    }

    public function getFirstDefinition(): SpanNode
    {
        if (!isset($this->definitions[0])) {
            throw new RuntimeException('No definitions found.');
        }

        return $this->definitions[0];
    }
}
