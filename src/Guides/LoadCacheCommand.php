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

namespace phpDocumentor\Guides;

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

    public function useCaching(): bool
    {
        return $this->useCaching;
    }
}
