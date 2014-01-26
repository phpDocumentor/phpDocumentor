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

namespace phpDocumentor\Descriptor;

/**
 * Analyzes a Project Descriptor and collects key information.
 *
 * This class can be used by external tools to analyze the Project Descriptor and collect key information from it such
 * as the total number of elements per type of Descriptor, the number of top level namespaces or the number of parent
 * classes that could not be interpreted by the Compiler passes.
 */
class ProjectAnalyzer
{
    /** @var integer $elementCount */
    protected $elementCount = 0;

    /** @var integer $fileCount */
    protected $fileCount = 0;

    /** @var integer $topLevelNamespaceCount */
    protected $topLevelNamespaceCount = 0;

    /** @var integer $unresolvedParentClassesCount */
    protected $unresolvedParentClassesCount = 0;

    /** @var integer[] $descriptorCountByType */
    protected $descriptorCountByType = array();

    /**
     * Analyzes the given project descriptor and populates this object's properties.
     *
     * @param ProjectDescriptor $projectDescriptor
     *
     * @return void
     */
    public function analyze(ProjectDescriptor $projectDescriptor)
    {
        $this->unresolvedParentClassesCount = 0;

        $elementCounter = array();
        foreach ($projectDescriptor->getIndexes()->elements as $element) {
            if (!isset($elementCounter[get_class($element)])) {
                $elementCounter[get_class($element)] = 0;
            }
            $elementCounter[get_class($element)]++;

            if ($element instanceof ClassDescriptor) {
                if (is_string($element->getParent())) {
                    $this->unresolvedParentClassesCount++;
                }
            }
        }

        $this->descriptorCountByType  = $elementCounter;
        $this->fileCount              = count($projectDescriptor->getFiles());
        $this->topLevelNamespaceCount = count($projectDescriptor->getNamespace()->getChildren());
    }

    /**
     * Returns a textual report of the findings of this class.
     *
     * @return string
     */
    public function __toString()
    {
        $logString = <<<TEXT
In the ProjectDescriptor are:
  %8d files
  %8d top-level namespaces
  %8d unresolvable parent classes

TEXT;
        foreach ($this->descriptorCountByType as $class => $count) {
            $logString .= sprintf('  %8d %s elements' . PHP_EOL, $count, $class);
        }

        return sprintf(
            $logString,
            $this->fileCount,
            $this->topLevelNamespaceCount,
            $this->unresolvedParentClassesCount
        );
    }
}
