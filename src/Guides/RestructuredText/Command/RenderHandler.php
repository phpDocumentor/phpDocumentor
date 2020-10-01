<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\Copier;
use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use Symfony\Component\Filesystem\Filesystem;

final class RenderHandler
{
    /** @var Copier */
    private $copier;

    /** @var Documents */
    private $documents;

    /** @var Metas */
    private $metas;

    /** @var CachedMetasLoader */
    private $cachedMetasLoader;

    public function __construct(
        Documents $documents,
        Filesystem $filesystem,
        Metas $metas,
        CachedMetasLoader $cachedMetasLoader
    ) {
        $this->copier = new Copier($filesystem);
        $this->documents = $documents;
        $this->metas = $metas;
        $this->cachedMetasLoader = $cachedMetasLoader;
    }

    public function handle(RenderCommand $command) : void
    {
        $this->render($command->getDirectory(), $command->getOutputDirectory());

        $this->cachedMetasLoader->cacheMetaEntries($command->getOutputDirectory(), $this->metas);
    }

    private function render(string $directory, string $targetDirectory) : void
    {
        $this->documents->render($targetDirectory);

        $this->copier->doMkdir($targetDirectory);
        $this->copier->doCopy($directory, $targetDirectory);
    }
}
