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

namespace phpDocumentor\Application;

use Stash\Pool;

final class ConfigureCacheHandler
{
    /** @var Pool */
    private $pool;

    /**
     * ConfigureCacheHandler constructor.
     */
    public function __construct(Pool $pool)
    {
        $this->pool = $pool;
    }

    public function __invoke(ConfigureCache $command)
    {
        $this->pool->getDriver()->setOptions(['path' => $command->location()]);
        if ($command->enabled() === false) {
            $this->pool->flush();
        }
    }
}
