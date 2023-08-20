<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use DirectoryIterator;

interface ExtensionLoader
{
    public function supports(DirectoryIterator $dir): bool;

    public function load(DirectoryIterator $dir): ExtensionInfo|null;
}
