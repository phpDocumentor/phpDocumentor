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

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\Example\Finder;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;

/**
 * This index builder collects all examples from tags and inserts them into the example index.
 */
class ExampleTagsEnricher implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9002;

    /** @var ExampleAssembler */
    private $exampleAssembler;

    /**
     * Initializes this compiler pass with its dependencies.
     *
     * @param Finder $finder Finds examples in several directories.
     */
    public function __construct(Finder $finder)
    {
        $this->exampleAssembler = new ExampleAssembler($finder);
    }

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Enriches inline example tags with their sources';
    }

    /**
     * {@inheritDoc}
     */
    public function execute(ProjectDescriptor $project)
    {
        $elements = $project->getIndexes()->get('elements');

        /** @var DescriptorAbstract $element */
        foreach ($elements as $element) {
            $element->setDescription($this->replaceInlineExamples($element));
        }
    }

    /**
     * Replaces the example tags in the description with the contents of the found example.
     *
     * @param DescriptorAbstract $element
     *
     * @return string
     */
    protected function replaceInlineExamples(DescriptorAbstract $element)
    {
        $description = $element->getDescription();
        $matches     = array();

        if (! $description
            || ! preg_match_all('/\{@example\s(.+?)\}/', $description, $matches)
            || count($matches[0]) < 1
        ) {
            return $description;
        }

        $matched = array();
        foreach ($matches[0] as $index => $match) {
            if (isset($matched[$match])) {
                continue;
            }

            $matched[$match] = true;
            $exampleReflector = new ExampleTag('example', $matches[1][$index]);

            $example = $this->exampleAssembler->create($exampleReflector);

            $replacement = '`'.$example->getExample().'`';
            if ($example->getDescription()) {
                $replacement = '*' . $example->getDescription() . '*' . $replacement;
            }

            $description = str_replace($match, $replacement, $description);
        }

        return $description;
    }
}
