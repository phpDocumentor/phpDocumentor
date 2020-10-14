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

final class PersistCacheCommand
{
    /** @var string */
    private $cacheDirectory;

    /** @var bool */
    private $useCache;

    public function __construct(string $cacheDirectory, bool $useCache = false)
    {
        $this->cacheDirectory = $cacheDirectory;
        $this->useCache = $useCache;
    }

    public function getCacheDirectory() : string
    {
        return $this->cacheDirectory;
    }

    public function useCache() : bool
    {
        return $this->useCache;
    }
}
