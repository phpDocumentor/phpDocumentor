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

class BlockNode extends Node
{
    /**
     * @param string[] $lines
     */
    public function __construct(array $lines)
    {
        parent::__construct($this->normalizeLines($lines));
    }
}
