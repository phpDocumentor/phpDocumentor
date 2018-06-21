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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;
use Psr\Log\LoggerInterface;

/**
 * This class is responsible for sending statistical information to the log.
 *
 * For debugging purposes it can be convenient to send statistical information about the
 * ProjectDescriptor to the log of phpDocumentor.
 */
class Debug implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 1000;

    /** @var LoggerInterface $log the logger to write the debug results to */
    protected $log;

    /** @var ProjectAnalyzer $analyzer service that compiles a summary of the project */
    protected $analyzer;

    /**
     * Registers the logger with this Compiler Pass.
     */
    public function __construct(LoggerInterface $log, ProjectAnalyzer $analyzer)
    {
        $this->log = $log;
        $this->analyzer = $analyzer;
    }

    public function getDescription(): string
    {
        return 'Analyze results and write report to log';
    }

    public function execute(ProjectDescriptor $project): void
    {
        $this->analyzer->analyze($project);
        $this->log->debug((string) $this->analyzer);
    }
}
