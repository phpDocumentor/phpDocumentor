<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\DocBlock\ExampleFinder;
use phpDocumentor\Reflection\DocBlock\Tags\Example;

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
     * @param ExampleFinder $finder Finds examples in several directories.
     */
    public function __construct(ExampleFinder $finder)
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
     * @return string
     */
    protected function replaceInlineExamples(DescriptorAbstract $element)
    {
        $description = $element->getDescription();
        $matches = [];

        if (! $description
            || ! preg_match_all('/\{@example\s(.+?)\}/', $description, $matches)
            || count($matches[0]) < 1
        ) {
            return $description;
        }

        $matched = [];
        foreach ($matches[0] as $index => $match) {
            if (isset($matched[$match])) {
                continue;
            }

            $matched[$match] = true;
            $exampleReflector = Example::create($matches[1][$index]);

            $example = $this->exampleAssembler->create($exampleReflector);

            $replacement = '`' . $example->getExample() . '`';
            if ($example->getDescription()) {
                $replacement = '*' . $example->getDescription() . '*' . $replacement;
            }

            $description = str_replace($match, $replacement, $description);
        }

        return $description;
    }
}
