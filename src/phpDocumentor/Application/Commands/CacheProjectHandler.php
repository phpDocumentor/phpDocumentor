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

use Desarrolla2\Cache\Adapter\File;
use Desarrolla2\Cache\Cache;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Caches the current Project.
 */
final class CacheProjectHandler
{
    /** @var Cache */
    private $cache;

    /** @var Analyzer */
    private $analyzer;

    /**
     * Initializes this handler with the required dependencies.
     *
     * @param Cache    $cache
     * @param Analyzer $analyzer
     */
    public function __construct(Cache $cache, Analyzer $analyzer)
    {
        $this->cache    = $cache;
        $this->analyzer = $analyzer;
    }

    /**
     * Caches the project.
     *
     * @param CacheProject $command
     *
     * @return void
     */
    public function __invoke(CacheProject $command)
    {
        $this->cache->setAdapter(new File($command->getTarget()));

        $projectDescriptor = $this->analyzer->getProjectDescriptor();
        $mapper = new ProjectDescriptorMapper($this->cache);
        $mapper->save($projectDescriptor);
    }
}
