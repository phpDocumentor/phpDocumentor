<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Handlers;

use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\PersistCacheCommand;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;

final class PersistCacheHandler
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

    public function handle(PersistCacheCommand $command)
    {
        if (!$command->useCache()) {
            return;
        }

        $this->cachedMetasLoader->cacheMetaEntries($command->getCacheDirectory(), $this->metas);
    }
}
