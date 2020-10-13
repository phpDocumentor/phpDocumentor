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

namespace phpDocumentor\Guides\Nodes;

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
