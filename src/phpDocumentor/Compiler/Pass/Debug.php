<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use Psr\Log\LoggerInterface;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ClassDescriptor;
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

    /** @var \Psr\Log\LoggerInterface $log */
    protected $log;

    /**
     * Registers the logger with this Compiler Pass.
     *
     * @param LoggerInterface $log
     */
    public function __construct(LoggerInterface $log)
    {
        $this->log = $log;
    }

    /**
     * {@inheritDoc}
     */
    public function execute(ProjectDescriptor $project)
    {
        $elementCounter = array();
        $unknownParentClasses = 0;
        foreach ($project->getIndexes()->elements as $element) {
            if (!isset($elementCounter[get_class($element)])) {
                $elementCounter[get_class($element)] = 0;
            }
            $elementCounter[get_class($element)]++;

            if ($element instanceof ClassDescriptor) {
                if (is_string($element->getParentClass())) {
                    $unknownParentClasses++;
                }
            }
        }


        $logString = <<<TEXT
In the ProjectDescriptor are:
  %8d files
  %8d top-level namespaces
  %8d unresolvable parent classes

TEXT;
        foreach ($elementCounter as $class => $count) {
            $logString .= sprintf('  %8d %s elements' . PHP_EOL, $count, $class);
        }

        $this->log->debug(
            sprintf(
                $logString,
                count($project->getFiles()),
                count($project->getNamespace()->getNamespaces()),
                $unknownParentClasses
            )
        );

    }
}
