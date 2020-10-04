<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Kernel;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;
use phpDocumentor\Guides\RestructuredText\Meta\Metas;

final class LoadCacheHandler
{
    /** @var CachedMetasLoader */
    private $cachedMetasLoader;

    /** @var Metas */
    private $metas;

    public function __construct(CachedMetasLoader $cachedMetasLoader, Metas $metas)
    {
        $this->metas = $metas;
        $this->cachedMetasLoader = $cachedMetasLoader;
    }

    public function handle(LoadCacheCommand $command): void
    {
        $configuration = $command->getKernel()->getConfiguration();

        if (!$configuration->getUseCachedMetas()) {
            return;
        }

        $this->cachedMetasLoader->loadCachedMetaEntries($command->getCacheDirectory(), $this->metas);
    }
}
