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

class FieldAccess implements PathNode
{
    private FieldName $fieldName;

    public function __construct(FieldName $param)
    {
        $this->fieldName = $param;
    }

    /** @inheritDoc */
    public function visit(Executor $param, $currentObject, $root)
    {
        return $param->evaluateFieldAccess($currentObject, $this->fieldName);
    }
}
