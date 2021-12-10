<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\Formats\OutputFormat as BaseFormat;

class OutputFormat implements BaseFormat
{
    /** @var string */
    private $fileExtension;

    public function __construct(string $fileExtension)
    {
        $this->fileExtension = $fileExtension;
    }

    public function getFileExtension(): string
    {
        return $this->fileExtension;
    }
}
