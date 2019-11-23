<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer;

use phpDocumentor\Transformer\Template\Parameter;

/**
 * Model representing a template.
 */
final class Template implements \ArrayAccess, \Countable, \IteratorAggregate
{
    /**
     * @var string Name for this template
     */
    protected $name = null;

    /**
     * @var string The name and optionally mail address of the author, i.e. `Mike van Riel <me@mikevanriel.com>`.
     */
    protected $author = '';

    /**
     * @var string The version of the template according to semantic versioning, i.e. 1.2.0
     */
    protected $version = '';

    /**
     * @var string A free-form copyright notice.
     */
    protected $copyright = '';

    /**
     * @var string a text providing more information on this template.
     */
    protected $description = '';

    /**
     * @var Transformation[] A series of transformations to execute in sequence during transformation.
     */
    protected $transformations = [];

    /**
     * @var Parameter[] Global parameters that are passed to each transformation.
     */
    protected $parameters = [];

    /**
     * Initializes this object with a name and optionally with contents.
     *
     * @param string $name Name for this template.
     */
    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * Name for this template.
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * The name of the author of this template (optionally including mail
     * address).
     *
     * @param string $author Name of the author optionally including mail address
     *  between angle brackets.
     */
    public function setAuthor(string $author)
    {
        $this->author = $author;
    }

    /**
     * Returns the name and/or mail address of the author.
     */
    public function getAuthor()
    {
        return $this->author;
    }

    /**
     * Sets the copyright string for this template.
     *
     * @param string $copyright Free-form copyright notice.
     */
    public function setCopyright(string $copyright)
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns the copyright string for this template.
     */
    public function getCopyright()
    {
        return $this->copyright;
    }

    /**
     * Sets the version number for this template.
     *
     * @param string $version Semantic version number in this format: 1.0.0
     *
     * @throws \InvalidArgumentException if the version number is invalid
     */
    public function setVersion(string $version)
    {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw new \InvalidArgumentException(
                'Version number is invalid; ' . $version . ' does not match '
                . 'x.x.x (where x is a number)'
            );
        }

        $this->version = $version;
    }

    /**
     * Returns the version number for this template.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the description for this template.
     *
     * @param string $description An unconstrained text field where the user can provide additional information
     *     regarding details of the template.
     */
    public function setDescription(string $description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this template.
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Sets a transformation at the given offset.
     *
     * @param integer|string $offset The offset to place the value at.
     * @param Transformation $value  The transformation to add to this template.
     *
     * @throws \InvalidArgumentException if an invalid item was received
     */
    public function offsetSet($offset, $value)
    {
        if (!$value instanceof Transformation) {
            throw new \InvalidArgumentException(
                '\phpDocumentor\Transformer\Template may only contain items of '
                . 'type \phpDocumentor\Transformer\Transformation'
            );
        }

        $this->transformations[$offset] = $value;
    }

    /**
     * Gets the transformation at the given offset.
     *
     * @param integer|string $offset The offset to retrieve from.
     *
     * @return Transformation
     */
    public function offsetGet($offset)
    {
        return $this->transformations[$offset];
    }

    /**
     * Offset to unset.
     *
     * @param integer|string $offset Index of item to unset.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetunset.php
     */
    public function offsetUnset($offset)
    {
        unset($this->transformations[$offset]);
    }

    /**
     * Whether a offset exists.
     *
     * @param mixed $offset An offset to check for.
     *
     * @link http://php.net/manual/en/arrayaccess.offsetexists.php
     *
     * @return boolean Returns true on success or false on failure.
     */
    public function offsetExists($offset)
    {
        return isset($this->transformations[$offset]);
    }

    /**
     * Count the number of transformations.
     *
     * @link http://php.net/manual/en/countable.count.php
     *
     * @return int The count as an integer.
     */
    public function count()
    {
        return count($this->transformations);
    }

    /**
     * Returns the parameters associated with this template.
     *
     * @return Parameter[]
     */
    public function getParameters(): array
    {
        return $this->parameters;
    }

    /**
     * Sets a new parameter in the collection.
     *
     * @param string|integer $key
     * @param Parameter      $value
     */
    public function setParameter($key, Parameter $value)
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Pushes the parameters of this template into the transformations.
     */
    public function propagateParameters()
    {
        foreach ($this->transformations as $transformation) {
            $transformation->setParameters(array_merge($transformation->getParameters(), $this->getParameters()));
        }
    }

    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->transformations);
    }
}
