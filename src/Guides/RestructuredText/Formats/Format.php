<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Formats;

use IteratorAggregate;
use phpDocumentor\Guides\Formats\Format as BaseFormat;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;

use function iterator_to_array;

abstract class Format implements BaseFormat
{
    /** @var string */
    private $fileExtension;

    /** @var IteratorAggregate<Directive> */
    private $directives;

    /**
     * @param IteratorAggregate<Directive> $directives
     */
    public function __construct(string $fileExtension, IteratorAggregate $directives)
    {
        $this->fileExtension = $fileExtension;
        $this->directives = $directives;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }

    /**
     * @return array<Directive>
     */
    public function getDirectives(): array
    {
        return iterator_to_array($this->directives);
    }
}
