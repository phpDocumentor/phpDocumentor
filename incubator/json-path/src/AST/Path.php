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

namespace phpDocumentor\JsonPath\AST;

use phpDocumentor\JsonPath\Executor;

class Path implements QueryNode
{
    /** @param non-empty-list<PathNode> $nodes */
    public function __construct(private readonly array $nodes)
    {
    }

    /** @return non-empty-list<PathNode>*/
    public function getNodes(): array
    {
        return $this->nodes;
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root)
    {
        return $param->evaluatePath($root, $currentObject, ...$this->nodes);
    }
}
