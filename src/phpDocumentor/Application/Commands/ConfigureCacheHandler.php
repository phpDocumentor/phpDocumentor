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

namespace phpDocumentor\Application\Commands;

use phpDocumentor\Application\Configuration\ConfigurationFactory;
use Stash\Pool;

final class ConfigureCacheHandler
{
    /**
     * @var Pool
     */
    private $pool;

    /**
     * @var ConfigurationFactory
     */
    private $configurationFactory;

    /**
     * ConfigureCacheHandler constructor.
     */
    public function __construct(Pool $pool, ConfigurationFactory $configurationFactory)
    {

        $this->pool = $pool;
        $this->configurationFactory = $configurationFactory;
    }

    public function __invoke()
    {
        $this->pool->getDriver()->setOptions(
            [
                'path' => $this->configurationFactory->get()['phpdocumentor']['paths']['cache']
            ]
        );

        if ($this->configurationFactory->get()['phpdocumentor']['use-cache'] === false) {
            $this->pool->flush();
        }
    }
}
