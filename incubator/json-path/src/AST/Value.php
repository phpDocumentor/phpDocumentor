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

class Value implements QueryNode
{
    public function __construct(private readonly mixed $value)
    {
    }

    /**
     * @param mixed $currentObject
     * @param mixed $root
     *
     * @return mixed
     */
    public function visit(Executor $param, $currentObject, $root)
    {
        return $this->value;
    }
}
