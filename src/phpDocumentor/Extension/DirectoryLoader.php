<?php

declare(strict_types=1);

namespace phpDocumentor\Extension;

use DirectoryIterator;
use PharIo\Manifest\Manifest;
use PharIo\Manifest\ManifestLoader;

final class DirectoryLoader implements ExtensionLoader
{
    public function supports(DirectoryIterator $dir): bool
    {
        return $dir->isDir() && $this->findManifestFile(new DirectoryIterator($dir->getPathname())) !== null;
    }

    public function loadManifest(DirectoryIterator $dir): ?Manifest
    {
        $file = $this->findManifestFile($dir);
        if ($file === null) {
            return null;
        }

        return ManifestLoader::fromFile($file->getPathName());
    }

    private function findManifestFile(DirectoryIterator $dir): ?DirectoryIterator
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
