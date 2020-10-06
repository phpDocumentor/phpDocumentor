<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Toc;

use phpDocumentor\Guides\Environment;
use Symfony\Component\Finder\Finder;
use function array_merge;
use function realpath;
use function rtrim;
use function str_replace;

class GlobSearcher
{
    /**
     * @return string[]
     */
    public function globSearch(Environment $environment, string $globPattern) : array
    {
        $currentFilePath = (string) realpath(rtrim($environment->absoluteRelativePath(''), '/'));

        $rootDocPath = rtrim(str_replace($environment->getDirName(), '', $currentFilePath), '/');

        if ($globPattern[0] === '/') {
            $globPatternPath = $rootDocPath . $globPattern;
        } else {
            $globPatternPath = $currentFilePath . '/' . $globPattern;
        }

        $allFiles = [];

        $finder = new Finder();
        $finder->in(rtrim($globPatternPath, '*'))
            ->name('*.rst')
            ->files();

        foreach ($finder as $file) {
            if ($file->isDir()) {
                // remove the root directory so it is a relative path from the root
                $relativePath = str_replace($rootDocPath, '', (string) $file->getRealPath());

                // recursively search in this directory
                $dirFiles = $this->globSearch($environment, $relativePath . '/*');

                $allFiles = array_merge($allFiles, $dirFiles);
            } else {
                // Trim the root path and the .rst extension. This is what the
                // RST parser requires to add a dependency.
                $file = str_replace([$rootDocPath, '.rst'], '', (string) $file->getRealPath());

                $allFiles[] = $file;
            }
        }

        return $allFiles;
    }
}
