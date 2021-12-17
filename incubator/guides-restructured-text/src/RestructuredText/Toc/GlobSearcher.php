<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Toc;

use Flyfinder\Specification\Glob;
use phpDocumentor\Guides\Environment;

use function dirname;
use function ltrim;
use function rtrim;
use function str_replace;

class GlobSearcher
{
    /**
     * @return string[]
     */
    public function globSearch(Environment $environment, string $globPattern): array
    {
        $fileSystem = $environment->getOrigin();
        $files = $fileSystem->find(
            new Glob(rtrim($environment->absoluteRelativePath(''), '/') . '/' . $globPattern)
        );
        $allFiles = [];
        foreach ($files as $file) {
            $allFiles[] = $environment->absoluteUrl(
                ltrim(
                    str_replace(
                        dirname($environment->getCurrentAbsolutePath()),
                        '',
                        $file['dirname'] . '/' . $file['filename']
                    ),
                    '/'
                )
            );
        }

        return $allFiles;
    }
}
