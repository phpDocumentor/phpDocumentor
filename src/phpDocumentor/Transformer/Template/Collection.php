<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Template;

use ArrayObject;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Transformer;
use phpDocumentor\Transformer\Writer\Collection as WriterCollection;

/**
 * Contains a collection of Templates that may be queried.
 */
class Collection extends ArrayObject
{
    /** @var Factory */
    private $factory;

    /** @var WriterCollection */
    private $writerCollection;

    /**
     * Constructs this collection and requires a factory to load templates.
     */
    public function __construct(Factory $factory, WriterCollection $writerCollection)
    {
        parent::__construct([]);
        $this->factory = $factory;
        $this->writerCollection = $writerCollection;
    }

    /**
     * Loads a template with the given name or file path.
     */
    public function load(Transformer $transformer, string $nameOrPath) : void
    {
        $template = $this->factory->get($transformer, $nameOrPath);

        /** @var Transformation $transformation */
        foreach ($template as $transformation) {
            $writer = $this->writerCollection[$transformation->getWriter()];
            $writer->checkRequirements();
        }

        $this[$template->getName()] = $template;
    }

    /**
     * Returns a list of all transformations contained in the templates of this collection.
     *
     * @return Transformation[]
     */
    public function getTransformations() : array
    {
        $result = [];
        foreach ($this as $template) {
            foreach ($template as $transformation) {
                $result[] = $transformation;
            }
        }

        return $result;
    }

    /**
     * Returns the path where all templates are stored.
     */
    public function getTemplatesPath() : string
    {
        return $this->factory->getTemplatesPath();
    }
}
