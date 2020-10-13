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

class SectionBeginNode extends Node
{
    /** @var TitleNode */
    private $titleNode;

    public function __construct(TitleNode $titleNode)
    {
        parent::__construct();

        $this->titleNode = $titleNode;
    }

    public function getTitleNode() : TitleNode
    {
        return $this->titleNode;
    }
}
