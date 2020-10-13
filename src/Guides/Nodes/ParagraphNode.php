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

class ParagraphNode extends Node
{
    /** @var SpanNode */
    protected $value;

    public function __construct(SpanNode $value)
    {
        parent::__construct($value);
    }

    public function getValue() : SpanNode
    {
        return $this->value;
    }
}
