<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

final class LoadCacheCommand
{
    /** @var string */
    private $cacheDirectory;

    /** @var bool */
    private $useCaching;

    public function __construct(string $cacheDirectory, bool $useCaching = true)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->useCaching = $useCaching;
    }

    public function getCacheDirectory(): string
    {
        return $this->cacheDirectory;
    }

    public function useCaching()
    {
        return $this->useCaching;
    }
}
