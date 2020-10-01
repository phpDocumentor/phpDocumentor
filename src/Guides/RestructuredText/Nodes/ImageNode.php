<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

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

        $this->url     = $url;
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
