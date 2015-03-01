<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.4
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * This class is responsible for sending statistical information to the standard output.
 *
 * For debugging purposes it can be convenient to send statistical information about the
 * ProjectDescriptor to the log of phpDocumentor.
 */
class Debug implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 1000;

    /** @var ProjectAnalyzer $analyzer service that compiles a summary of the project */
    protected $analyzer;

    /**
     * Registers the analyzer with this Compiler Pass.
     *
     * @param ProjectAnalyzer $analyzer
     */
    public function __construct(ProjectAnalyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Analyze results and write report to stdout';
    }

    /**
     * Analyzes the given project and returns the results to the stdout.
     *
     * @param ProjectDescriptor $project
     *
     * @return void
     */
    public function execute(ProjectDescriptor $project)
    {
        $this->analyzer->analyze($project);
        echo (string) $this->analyzer;
    }
}
