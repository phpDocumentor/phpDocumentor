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

namespace phpDocumentor\Transformer;

use phpDocumentor\Transformer\Template\Parameter;

/**
 * Class representing a single Transformation.
 */
class Transformation
{
    /** @var string Reference to an object containing the business logic used to execute this transformation. */
    private $writer = null;

    /** @var string the location where the output should be sent to; the exact function differs per writer. */
    private $artifact = '';

    /** @var string the location where input for a writer should come from; the exact function differs per writer. */
    private $source = '';

    /**
     * @var string a filter or other form of limitation on what information of the AST is used; the exact function
     *     differs per writer.
     */
    private $query = '';

    /** @var Transformer The object guiding the transformation process and having meta-data of it. */
    private $transformer;

    /**
     * @var Parameter[] A series of parameters that can influence what the writer does; the exact function differs
     *     per writer.
     */
    private $parameters = [];

    /** @var Template */
    private $template;

    /**
     * Constructs a new Transformation object and populates the required parameters.
     *
     * @param string $query What information to use as datasource for the writer's source.
     * @param string $writer What type of transformation to apply (PDF, Twig etc).
     * @param string $source Which template or type of source to use.
     * @param string $artifact What is the filename of the result (relative to the generated root)
     */
    public function __construct(Template $template, string $query, string $writer, string $source, string $artifact)
    {
        $this->template = $template;
        $this->setQuery($query);
        $this->setWriter($writer);
        $this->setSource($source);
        $this->setArtifact($artifact);
    }

    /**
     * Sets the query.
     *
     * @param string $query Free-form string with writer-specific values.
     */
    public function setQuery(string $query) : void
    {
        $this->query = $query;
    }

    /**
     * Returns the set query.
     */
    public function getQuery() : string
    {
        return $this->query;
    }

    /**
     * Sets the writer type and instantiates a writer.
     *
     * @param string $writer Name of writer to instantiate.
     */
    public function setWriter(string $writer) : void
    {
        $this->writer = $writer;
    }

    /**
     * Returns the class name of the associated writer.
     */
    public function getWriter() : string
    {
        return $this->writer;
    }

    /**
     * Sets the source / type which the writer will use to generate artifacts from.
     *
     * @param string $source Free-form string with writer-specific meaning.
     */
    public function setSource(string $source) : void
    {
        $this->source = $source;
    }

    /**
     * Returns the name of the source / type used in the transformation process.
     */
    public function getSource() : string
    {
        return $this->source;
    }

    public function template() : Template
    {
        return $this->template;
    }

    /**
     * Filename of the resulting artifact relative to the root.
     *
     * If the query results in a set of artifacts (multiple nodes / array);
     * then this string must contain an identifying variable as returned by the
     * writer.
     *
     * @param string $artifact Name of artifact to generate; usually a filepath.
     */
    public function setArtifact(string $artifact) : void
    {
        $this->artifact = $artifact;
    }

    /**
     * Returns the name of the artifact.
     */
    public function getArtifact() : string
    {
        return $this->artifact;
    }

    /**
     * Sets an array of parameters (key => value).
     *
     * @param Parameter[] $parameters Associative multidimensional array containing
     *     parameters for the Writer.
     */
    public function setParameters(array $parameters) : void
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns all parameters for this transformation.
     *
     * @return Parameter[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name Name of the parameter to return.
     */
    public function getParameter(string $name) : ?Parameter
    {
        /** @var Parameter $parameter */
        foreach ($this->parameters as $parameter) {
            if ($parameter->key() === $name) {
                return $parameter;
            }
        }

        return null;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name Name of the parameter to return.
     *
     * @return Parameter[]
     */
    public function getParametersWithKey(string $name) : array
    {
        $parameters = [];

        /** @var Parameter $parameter */
        foreach ($this->parameters as $parameter) {
            if ($parameter->key() !== $name) {
                continue;
            }

            $parameters[] = $parameter;
        }

        return $parameters;
    }

    /**
     * Sets the transformer on this transformation.
     */
    public function setTransformer(Transformer $transformer) : void
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer for this transformation.
     */
    public function getTransformer() : ?Transformer
    {
        return $this->transformer;
    }
}
