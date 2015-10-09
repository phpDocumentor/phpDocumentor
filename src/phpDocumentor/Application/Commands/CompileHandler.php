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
use phpDocumentor\Compiler\Compiler;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Compile the current Project to resolve links.
 */
final class CompileHandler
{
    /** @var Analyzer */
    private $analyzer;

    /** @var Compiler */
    private $compiler;

    /**
     * Initializes this handler with the required dependencies.
     *
     * @param Analyzer $analyzer
     * @param Compiler $compiler
     */
    public function __construct(Analyzer $analyzer, Compiler $compiler)
    {
        $this->analyzer = $analyzer;
        $this->compiler = $compiler;
    }

    /**
     * Caches the project.
     *
     * @param Compile $command
     *
     * @return void
     */
    public function __invoke(Compile $command)
    {
        $projectDescriptor = $this->analyzer->getProjectDescriptor();

        /** @var CompilerPassInterface $pass */
        foreach (clone $this->compiler as $pass) {
            $pass->execute($projectDescriptor);
        }
    }
}
