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

namespace phpDocumentor\Transformer\Template;

use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;
use phpDocumentor\Transformer\Writer\WriterAbstract;

/**
 * Contains a collection of Templates that may be queried.
 */
class Collection extends \ArrayObject
{
    /** @var Factory */
    private $factory;

    /** @var WriterCollection */
    private $writerCollection;

    /**
     * Constructs this collection and requires a factory to load templates.
     *
     * @param Factory          $factory
     * @param WriterCollection $writerCollection
     */
    public function __construct(Factory $factory, WriterCollection $writerCollection)
    {
        $this->factory          = $factory;
        $this->writerCollection = $writerCollection;
    }

    /**
     * Loads a template with the given name or file path.
     *
     * @param string $nameOrPath
     *
     * @return void
     */
    public function load($nameOrPath)
    {
        $template = $this->factory->get($nameOrPath);

        /** @var Transformation $transformation */
        foreach ($template as $transformation) {
            /** @var WriterAbstract $writer */
            $writer = $this->writerCollection[$transformation->getWriter()];
            if ($writer) {
                $writer->checkRequirements();
            }
        }

        $this[$template->getName()] = $template;
    }

    /**
     * Returns a list of all transformations contained in the templates of this collection.
     *
     * @return Transformation[]
     */
    public function getTransformations()
    {
        $result = array();
        foreach ($this as $template) {
            foreach ($template as $transformation) {
                $result[] = $transformation;
            }
        }

        return $result;
    }

    /**
     * Returns the path where all templates are stored.
     *
     * @return string
     */
    public function getTemplatesPath()
    {
        return $this->factory->getTemplatePath();
    }
}
