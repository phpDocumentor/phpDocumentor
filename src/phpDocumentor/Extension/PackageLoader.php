<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ManifestLoader;
use SplFileInfo;

use function file_exists;

use const DIRECTORY_SEPARATOR;

final class PackageLoader implements ExtensionLoader
{
    public function supports(SplFileInfo $dir): bool
    {
        return $dir->isDir() && file_exists($dir->getPathname() . DIRECTORY_SEPARATOR . 'manifest.xml');
    }

    public function load(SplFileInfo $dir): ExtensionInfo|null
    {
        return ExtensionInfo::fromManifest(
            ManifestLoader::fromFile($dir->getPathname() . DIRECTORY_SEPARATOR . 'manifest.xml'),
            $dir->getPathname(),
        );
    }
}
