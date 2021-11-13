<?php

declare(strict_types=1);

namespace phpDocumentor\Wordpress\Reflection\Php;

use phpDocumentor\Reflection\DocBlock;

final class Action
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var DocBlock
     */
    private $docBlock;

    public function __construct(string $name, DocBlock $docBlock)
    {
        $this->name = $name;
        $this->docBlock = $docBlock;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDocBlock(): DocBlock
    {
        return $this->docBlock;
    }
}
