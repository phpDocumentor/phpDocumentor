<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Toc;

use Flyfinder\Specification\Glob;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\UrlGenerator;

use function rtrim;

class GlobSearcher
{
    /** @var UrlGenerator */
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    /**
     * @return string[]
     */
    public function globSearch(ParserContext $environment, string $globPattern): array
    {
        $fileSystem = $environment->getOrigin();
        $files = $fileSystem->find(
            new Glob(rtrim($environment->absoluteRelativePath(''), '/') . '/' . $globPattern)
        );
        $allFiles = [];
        foreach ($files as $file) {
            $allFiles[] = $this->urlGenerator->absoluteUrl($environment->getDirName(), $file['filename']);
        }

        return $allFiles;
    }
}
