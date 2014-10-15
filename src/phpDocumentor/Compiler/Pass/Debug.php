<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use Psr\Log\LoggerInterface;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;

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
     *
     * @param LoggerInterface $log
     * @param ProjectAnalyzer $analyzer
     */
    public function __construct(LoggerInterface $log, ProjectAnalyzer $analyzer)
    {
        $this->log      = $log;
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Analyze results and write report to log';
    }

    /**
     * Analyzes the given project and returns the results to the logger.
     *
     * @param ProjectDescriptor $project
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
    {
        $this->analyzer->analyze($project);
        $this->log->debug((string) $this->analyzer);
    }
}
