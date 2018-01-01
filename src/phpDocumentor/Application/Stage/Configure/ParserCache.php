<?php
/**
 * This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 *  @copyright 2010-2017 Mike van Riel<mike@phpdoc.org>
 *  @license   http://www.opensource.org/licenses/mit-license.php MIT
 *  @link      http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage\Configure;


use phpDocumentor\Parser\Middleware\CacheMiddleware;

final class ParserCache
{
    /**
     * @var CacheMiddleware
     */
    private $cacheMiddleware;

    /**
     * ParserCache constructor.
     * @param CacheMiddleware $cacheMiddleware
     */
    public function __construct(CacheMiddleware $cacheMiddleware)
    {
        $this->cacheMiddleware = $cacheMiddleware;
    }


    /**
     * @return array
     */
    public function __invoke(array $configuration): array
    {
        if (!$configuration['phpdocumentor']['use-cache']) {
            $this->cacheMiddleware->disable();
        }

        return $configuration;
    }
}
