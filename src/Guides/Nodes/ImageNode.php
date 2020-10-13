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

class ImageNode extends Node
{
    /** @var string */
    protected $url;

    /** @var string[] */
    protected $options;

    /**
     * @param string[] $options
     */
    public function __construct(string $url, array $options = [])
    {
        parent::__construct();

        $this->url = $url;
        $this->options = $options;
    }

    public function getUrl() : string
    {
        return $this->url;
    }

    /**
     * @return string[]
     */
    public function getOptions() : array
    {
        return $this->options;
    }
}
