<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use DirectoryIterator;
use PharIo\Manifest\Manifest;

interface ExtensionLoader
{
    public function supports(DirectoryIterator $dir): bool;

    public function load(DirectoryIterator $dir): ?Extension;
}
