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

namespace phpDocumentor\Transformer;

use phpDocumentor\Transformer\Template\Parameter;

/**
 * Class representing a single Transformation.
 */
class Transformation
{
    /** @var string Reference to an object containing the business logic used to execute this transformation. */
    private $writer;

    /** @var string the location where the output should be sent to; the exact function differs per writer. */
    private $artifact;

    /** @var string the location where input for a writer should come from; the exact function differs per writer. */
    private $source;

    /**
     * @var string a filter or other form of limitation on what information of the AST is used; the exact function
     *     differs per writer.
     */
    private $query;

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
        $this->query = $query;
        $this->writer = $writer;
        $this->source = $source;
        $this->artifact = $artifact;
    }

    /**
     * Returns the set query.
     */
    public function getQuery(): string
    {
        return $this->query;
    }

    /**
     * Returns the class name of the associated writer.
     */
    public function getWriter(): string
    {
        return $this->writer;
    }

    /**
     * Returns the name of the source / type used in the transformation process.
     */
    public function getSource(): string
    {
        return $this->source;
    }

    public function template(): Template
    {
        return $this->template;
    }

    /**
     * Returns the name of the artifact.
     */
    public function getArtifact(): string
    {
        return $this->artifact;
    }

    /**
     * Sets an array of parameters (key => value).
     *
     * @param Parameter[] $parameters Associative multidimensional array containing
     *     parameters for the Writer.
     */
    public function setParameters(array $parameters): void
    {
        $this->parameters = $parameters;
    }

    /**
     * Returns all parameters for this transformation.
     *
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Returns a specific parameter, or $default if none exists.
     *
     * @param string $name Name of the parameter to return.
     */
    public function getParameter(string $name): ?Parameter
    {
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
    public function getParametersWithKey(string $name): array
    {
        $parameters = [];

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
    public function setTransformer(Transformer $transformer): void
    {
        $this->transformer = $transformer;
    }

    /**
     * Returns the transformer for this transformation.
     */
    public function getTransformer(): ?Transformer
    {
        return $this->transformer;
    }
}
