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

class QuoteNode extends Node
{
    /** @var DocumentNode */
    protected $value;

    public function __construct(DocumentNode $documentNode)
    {
        parent::__construct($documentNode);
    }

    public function getValue() : DocumentNode
    {
        return $this->value;
    }
}
