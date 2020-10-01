<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Nodes;

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
