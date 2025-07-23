<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use SplFileInfo;

interface ExtensionLoader
{
    public function supports(SplFileInfo $dir): bool;

    public function load(SplFileInfo $dir): ExtensionInfo|null;
}
