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

/**
 * This index builder collects all examples from tags and inserts them into the example index.
 */
class ExampleTagsEnricher implements CompilerPassInterface
{
    const COMPILER_PRIORITY = 9002;

    /**
     * @var string
     */
    protected $sourceDirectory = '';

    /**
     * @var string
     */
    protected $exampleDirectory = '';

    /**
     * {@inheritDoc}
     */
    public function getDescription()
    {
        return 'Enriches all example tags with their sources';
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

            $examples = $element->getTags()->get('example', array());

            /** @var ExampleDescriptor $example */
            foreach ($examples as &$example) {
                $example->setContent($this->getExampleContent($example));
            }

            $element->getTags()->set('example', $examples);
        }
    }

    /**
     * @param ExampleDescriptor $example
     *
     * @return string
     */
    protected function getExampleContent(ExampleDescriptor $example)
    {
        $filename = $example->getFilePath();

        $file = array();

        if (is_file($this->getExamplePathFromConfig($filename))) {
            $file = file($this->getExamplePathFromConfig($filename));
        } elseif (is_file($this->getExamplePathFromSource($filename))) {
            $file = file($this->getExamplePathFromSource($filename));
        } elseif (is_file($this->getExamplePath($filename))) {
            $file = file($this->getExamplePath($filename));
        } else {
            $file = @file($filename);
        }

        if (empty($file)) {
            $content = "** File not found : {$filename} ** ";
        } else {
            $filepart = array_slice($file, $example->getStartingLine(), $example->getLineCount());
            $content = implode('', $filepart);
        }

        return $content;
    }

    /**
     * @param DescriptorAbstract $element
     *
     * @return Ambigous <mixed, string>
     */
    protected function replaceInlineExamples(DescriptorAbstract $element)
    {
        $description = $element->getDescription();

        if ($description !== '' && preg_match('/\{@example\s(.+?)\}/', $description, $params)) {

            $example = $this->buildExampleDescriptor($params[1]);

            $replacement = sprintf(
                '<i>%s</i><pre>%s</pre>',
                $example->getDescription(),
                $this->getExampleContent($example)
            );

            $description = preg_replace('/\{@example\s(.+?)\}/', $replacement, $description);
        }

        return $description;
    }

    /**
     * @param array $params
     *
     * @return \phpDocumentor\Descriptor\Tag\ExampleDescriptor
     */
    protected function buildExampleDescriptor($paramString)
    {
        $params = explode(' ', $paramString);

        $example = new ExampleDescriptor('example');
        $example->setFilePath($params[0]);
        $example->setStartingLine(isset($params[1]) && is_numeric($params[1]) ? (int) $params[1] : 1);
        $example->setLineCount(isset($params[2]) && is_numeric($params[2]) ? (int) $params[2] : null);

        if (isset($params[3])) {

            $example->setDescription(strstr($paramString, $params[3]));

        } elseif (isset($params[2]) && !is_numeric($params[2])) {

            $example->setDescription(strstr($paramString, $params[2]));

        } elseif (isset($params[1]) && !is_numeric($params[1])) {

            $example->setDescription(strstr($paramString, $params[1]));

        }

        return $example;
    }

    /**
     * @param string $sourceDir
     *
     * @return void
     */
    public function setSourceDirectory($sourceDir)
    {
        $this->sourceDirectory = $sourceDir;
    }

    /**
     * @param string $exampleDir
     */
    public function setExampleDirectory($exampleDir)
    {
        $this->exampleDirectory = $exampleDir;
    }

    /**
     * Get example filepath based on the example directory inside your project.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePath($file)
    {
        return getcwd() . DIRECTORY_SEPARATOR . 'examples' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get example filepath based on config.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePathFromConfig($file)
    {
        return rtrim($this->exampleDirectory, '\\/') . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Get example filepath based on sourcecode.
     *
     * @param string $file
     *
     * @return string
     */
    protected function getExamplePathFromSource($file)
    {
        return sprintf(
            '%s%s%s%s%s',
            getcwd(),
            DIRECTORY_SEPARATOR,
            trim($this->sourceDirectory, '\\/'),
            DIRECTORY_SEPARATOR,
            trim($file, '"')
        );
    }
}
