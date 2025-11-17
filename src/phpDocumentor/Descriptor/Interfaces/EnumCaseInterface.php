<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\Interfaces;

use phpDocumentor\Reflection\Php\Expression;

interface EnumCaseInterface extends ElementInterface, ChildInterface, AttributedInterface
{
    public function setFile(FileInterface $file): void;

    public function getValue(): Expression|null;
}
