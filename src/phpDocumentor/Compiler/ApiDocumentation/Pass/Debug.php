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

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\ApiDocumentation\ApiDocumentationPass;
use phpDocumentor\Descriptor\Interfaces\ApiDocumentationSet;
use phpDocumentor\Descriptor\ProjectAnalyzer;
use phpDocumentor\Pipeline\Attribute\Stage;
use Psr\Log\LoggerInterface;

/**
 * This class is responsible for sending statistical information to the log.
 *
 * For debugging purposes it can be convenient to send statistical information about the
 * ProjectDescriptor to the log of phpDocumentor.
 */
#[Stage(
    'phpdoc.pipeline.api_documentation.compile',
    1000,
    'Analyze results and write report to log',
)]
final class Debug extends ApiDocumentationPass
{
    /** @var LoggerInterface $log the logger to write the debug results to */
    protected $log;

    /** @var ProjectAnalyzer $analyzer service that compiles a summary of the project */
    protected $analyzer;

    /**
     * Registers the logger with this Compiler Pass.
     */
    public function __construct(LoggerInterface $log, ProjectAnalyzer $analyzer)
    {
        $this->log      = $log;
        $this->analyzer = $analyzer;
    }

    protected function process(ApiDocumentationSet $subject): ApiDocumentationSet
    {
        $this->analyzer->analyze($subject);
        $this->log->debug((string) $this->analyzer);

        return $subject;
    }
}
