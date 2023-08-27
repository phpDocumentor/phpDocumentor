<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use DirectoryIterator;
use PharIo\Manifest\ManifestLoader;

final class DirectoryLoader implements ExtensionLoader
{
    public function supports(DirectoryIterator $dir): bool
    {
        return $dir->isDir() && $this->findManifestFile(new DirectoryIterator($dir->getPathname())) !== null;
    }

    public function load(DirectoryIterator $dir): ExtensionInfo|null
    {
        $file = $this->findManifestFile($dir);
        if ($file === null) {
            return null;
        }

        return ExtensionInfo::fromManifest(ManifestLoader::fromFile($file->getPathName()), $file->getPath());
    }

    private function findManifestFile(DirectoryIterator $dir): DirectoryIterator|null
    {
        foreach ($dir as $file) {
            if ($file->isDot()) {
                continue;
            }

            if ($file->isFile() === false) {
                continue;
            }

            if ($file->getFileName() !== 'manifest.xml') {
                continue;
            }

            return $file;
        }

        return null;
    }
}
