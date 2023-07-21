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

namespace phpDocumentor\JsonPath;

use phpDocumentor\JsonPath\AST\QueryNode;

final class Query implements QueryNode
{
    public function __construct(private readonly QueryNode $node)
    {
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root)
    {
        return $param->evaluate($this->node, $currentObject, $root);
    }
}
