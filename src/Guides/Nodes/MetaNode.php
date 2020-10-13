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

class MetaNode extends Node
{
    /** @var string */
    protected $key;

    /** @var string */
    protected $value;

    public function __construct(string $key, string $value)
    {
        parent::__construct();

        $this->key = $key;
        $this->value = $value;
    }

    public function getKey() : string
    {
        return $this->key;
    }

    public function getValue() : string
    {
        return $this->value;
    }
}
