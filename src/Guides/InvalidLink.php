<?php

declare(strict_types=1);

namespace phpDocumentor\Guides;

class InvalidLink
{
    /** @var string */
    private $name;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function getName() : string
    {
        return $this->name;
    }
}
