<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use PharIo\Manifest\ManifestLoader;
use SplFileInfo;

use function is_file;

final class PharLoader implements ExtensionLoader
{
    public function supports(SplFileInfo $dir): bool
    {
        return $dir->isFile() && $dir->getExtension() === 'phar' &&
            $this->findManifestFile($dir->getPathname()) !== null;
    }

    public function load(SplFileInfo $dir): ExtensionInfo|null
    {
        require_once $dir->getPathname();

        return $this->findManifestFile($dir->getPathname());
    }

    private function findManifestFile(string $getPathname): ExtensionInfo|null
    {
        $manifestPath = 'phar://' . $getPathname . '/manifest.xml';

        if (! is_file($manifestPath)) {
            return null;
        }

        return ExtensionInfo::fromManifest(ManifestLoader::fromFile($manifestPath), $getPathname);
    }
}
