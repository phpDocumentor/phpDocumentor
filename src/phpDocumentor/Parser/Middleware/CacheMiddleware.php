<?php

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use Stash\Item;
use Stash\Pool;

final class CacheMiddleware implements Middleware
{
    /**
     * Cache namespace used for this repository.
     */
    const CACHE_NAMESPACE = 'Documentation\\Api\\Php';

    /**
     * Cache pool used to store files.
     *
     * @var Pool
     */
    private $dataStore;

    private $enabled = true;

    public function __construct(Pool $dataStore)
    {
        $this->dataStore = $dataStore;
    }

    public function disable()
    {
        $this->enabled = false;
    }

    /**
     * Executes this middle ware class.
     * A middle ware class MUST return a File object or call the $next callable.
     *
     * @param CreateCommand $command
     * @param callable $next
     *
     * @return File
     */
    public function execute($command, callable $next)
    {
        $itemName = $this->getItemName($command->getFile()->path());
        $item = $this->dataStore->getItem($itemName);
        if ($item->isMiss() || $this->enabled === false) {
            return $this->updateCache($command, $next, $item);
        }

        /** @var File $cachedFile */
        $cachedFile = $item->get();

        if ($cachedFile === null) {
            return $this->updateCache($command, $next, $item);
        }

        if ($cachedFile->getHash() !== $command->getFile()->md5()) {
            return $this->updateCache($command, $next, $item);
        }

        return $cachedFile;
    }

    /**
     * @param CreateCommand $command
     * @param callable $next
     * @param Item $item
     * @return mixed
     */
    private function updateCache(CreateCommand $command, callable $next, $item)
    {
        $file = $next($command);
        $item->lock();
        $this->dataStore->save($item->set($file));
        return $file;
    }

    /**
     * Convert path to ItemName
     *
     * @param path
     * @return string
     */
    private function getItemName($path)
    {
        return static::CACHE_NAMESPACE . '\\' . md5($path);
    }
}
