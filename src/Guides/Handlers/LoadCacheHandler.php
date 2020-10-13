<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\Handlers;

use phpDocumentor\Guides\LoadCacheCommand;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\RestructuredText\Meta\CachedMetasLoader;

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
        if (!$command->useCaching()) {
            return;
        }

        $this->cachedMetasLoader->loadCachedMetaEntries($command->getCacheDirectory(), $this->metas);
    }
}
