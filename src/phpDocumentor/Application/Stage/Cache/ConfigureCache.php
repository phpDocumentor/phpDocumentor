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

namespace phpDocumentor\Application\Stage\Cache;

use phpDocumentor\Application\Stage\Payload;
use RuntimeException;
use Stash\Driver\FileSystem;
use Stash\Pool;

final class ConfigureCache
{
    private $cache;

    public function __construct(Pool $cache)
    {
        $this->cache = $cache;
    }

    /**
     * Executes the business logic involved with this command.
     *
     * @throws RuntimeException if the target location is not a folder.
     */
    public function __invoke(Payload $payload): Payload
    {
        $configuration  = $payload->getConfig();
        $target = (string) $configuration['phpdocumentor']['paths']['cache'];
        $this->cache->setDriver(new FileSystem(['path' => $target]));

        return $payload;
    }
}
