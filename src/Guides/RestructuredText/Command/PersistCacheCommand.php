<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

final class PersistCacheCommand
{
    private $cacheDirectory;

    /** @var bool */
    private $useCache;

    public function __construct(string $cacheDirectory, bool $useCache = false)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->useCache = $useCache;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function useCache(): bool
    {
        return $this->useCache;
    }
}
