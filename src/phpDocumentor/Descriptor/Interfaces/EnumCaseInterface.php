<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

interface EnumCaseInterface extends ElementInterface, ChildInterface
{
    public function setFile(FileInterface $file): void;

    public function getValue(): string|null;
}
