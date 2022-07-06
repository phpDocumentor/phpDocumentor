<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ApiAnalyzer;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use Psr\Log\LoggerInterface;

/**
 * This class is responsible for sending statistical information to the log.
 *
 * For debugging purposes it can be convenient to send statistical information about the
 * ProjectDescriptor to the log of phpDocumentor.
 */
class Debug implements CompilerPassInterface
{
    public const COMPILER_PRIORITY = 1000;

    /** @var LoggerInterface $log the logger to write the debug results to */
    protected $log;

    /** @var ApiAnalyzer $analyzer service that compiles a summary of the project */
    protected $analyzer;

    /**
     * Registers the logger with this Compiler Pass.
     */
    public function __construct(LoggerInterface $log, ApiAnalyzer $analyzer)
    {
        $this->log      = $log;
        $this->analyzer = $analyzer;
    }

    public function getDescription(): string
    {
        return 'Analyze results and write report to log';
    }

    public function execute(DocumentationSetDescriptor $project): void
    {
        if ($project instanceof ApiSetDescriptor === false) {
            return;
        }

        $this->analyzer->analyze($project);
        $this->log->debug((string) $this->analyzer);
    }
}
