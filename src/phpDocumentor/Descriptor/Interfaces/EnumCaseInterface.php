<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Descriptor\FileDescriptor;

interface EnumCaseInterface extends ElementInterface
{
    public function setFile(FileDescriptor $file): void;

    public function getValue(): ?string;
}
