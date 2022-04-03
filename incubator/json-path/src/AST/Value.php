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
    /** @var mixed */
    private $value;

    /** @param mixed $value */
    public function __construct($value)
    {
        $this->value = $value;
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
