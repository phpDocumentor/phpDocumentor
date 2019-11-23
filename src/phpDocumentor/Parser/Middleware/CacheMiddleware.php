<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser\Middleware;

use phpDocumentor\Parser\Parser;
use phpDocumentor\Reflection\Middleware\Command;
use phpDocumentor\Reflection\Middleware\Middleware;
use phpDocumentor\Reflection\Php\Factory\File\CreateCommand;
use phpDocumentor\Reflection\Php\File;
use Stash\Interfaces\PoolInterface;
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

    /**
     * @var Parser
     */
    private $parser;

    public function __construct(PoolInterface $dataStore, Parser $parser)
    {
        $this->dataStore = $dataStore;
        $this->parser = $parser;
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
    public function execute(Command $command, callable $next)
    {
        $itemName = $this->getItemName($command->getFile()->path());
        $item = $this->dataStore->getItem($itemName);
        if ($item->isMiss() || $this->parser->isForced()) {
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
     * @param string $path
     * @return string
     */
    private function getItemName($path)
    {
        return static::CACHE_NAMESPACE . '\\' . md5($path);
    }
}
