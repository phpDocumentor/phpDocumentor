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

class DummyNode extends Node
{
    /** @var mixed[] */
    public $data;

    /**
     * @param mixed[] $data
     */
    public function __construct(array $data)
    {
        parent::__construct();

        $this->data = $data;
    }

    protected function doRender() : string
    {
        return '';
    }
}
