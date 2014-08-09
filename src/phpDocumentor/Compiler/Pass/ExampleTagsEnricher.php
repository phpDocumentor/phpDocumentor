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

use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\TagDescriptor;
use phpDocumentor\Compiler\CompilerPassInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Reflection\DocBlock\Tag\ExampleTag;
use phpDocumentor\Descriptor\Builder\Reflector\Tags\ExampleAssembler;
use phpDocumentor\Descriptor\Collection;

/**
 * This index builder collects all examples from tags and inserts them into the example index.
 */
class ExampleTagsEnricher implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9002;

    public function __construct($sourceDir = '', $exampleDir = '')
    {
        ExampleAssembler::setSourceDirectory($sourceDir);
        ExampleAssembler::setExampleDirectory($exampleDir);
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
     * @param DescriptorAbstract $element
     *
     * @return Ambigous <mixed, string>
     */
    protected function replaceInlineExamples(DescriptorAbstract $element)
    {
        $description = $element->getDescription();

        if (!empty($description)
            && preg_match_all('/\{@example\s(.+?)\}/', $description, $matches)
            && count($matches[0]) >= 1) {

            $matched = array();

            foreach ($matches[0] as $index => $match) {
                if (!isset($matched[$match])) {
                    $matched[$match] = 1;
                    $exampleAssembler = new ExampleAssembler();
                    $exampleReflector = new ExampleTag('example', $matches[1][$index]);

                    $example = $exampleAssembler->create($exampleReflector);

                    $replacement = sprintf(
                        '<i>%s</i><pre>%s</pre>',
                        $example->getDescription(),
                        $example->getExample()
                    );

                    $description = str_replace($match, $replacement, $description);
                }
            }
        }

        return $description;
    }
}