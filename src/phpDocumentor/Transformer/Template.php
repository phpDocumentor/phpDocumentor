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

use ArrayAccess;
use ArrayIterator;
use Countable;
use InvalidArgumentException;
use IteratorAggregate;
use League\Flysystem\MountManager;
use phpDocumentor\Transformer\Template\Parameter;
use function array_merge;
use function count;
use function preg_match;

/**
 * Model representing a template.
 *
 * @template-implements ArrayAccess<int|string, Transformation>
 * @template-implements IteratorAggregate<int|string, Transformation>
 */
final class Template implements ArrayAccess, Countable, IteratorAggregate
{
    /** @var string Name for this template */
    private $name;

    /** @var string The name and optionally mail address of the author, i.e. `Mike van Riel <me@mikevanriel.com>`. */
    private $author = '';

    /** @var string The version of the template according to semantic versioning, i.e. 1.2.0 */
    private $version = '';

    /** @var string A free-form copyright notice. */
    private $copyright = '';

    /** @var string a text providing more information on this template. */
    private $description = '';

    /** @var Transformation[] A series of transformations to execute in sequence during transformation. */
    private $transformations = [];

    /** @var Parameter[] Global parameters that are passed to each transformation. */
    private $parameters = [];

    /** @var MountManager */
    private $files;

    /**
     * Initializes this object with a name and optionally with contents.
     *
     * @param string $name Name for this template.
     */
    public function __construct(string $name, MountManager $files)
    {
        $this->name = $name;
        $this->files = $files;
    }

    /**
     * Name for this template.
     */
    public function getName() : string
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
    public function setAuthor(string $author) : void
    {
        $this->author = $author;
    }

    /**
     * Returns the name and/or mail address of the author.
     */
    public function getAuthor() : string
    {
        return $this->author;
    }

    /**
     * Sets the copyright string for this template.
     *
     * @param string $copyright Free-form copyright notice.
     */
    public function setCopyright(string $copyright) : void
    {
        $this->copyright = $copyright;
    }

    /**
     * Returns the copyright string for this template.
     */
    public function getCopyright() : string
    {
        return $this->copyright;
    }

    /**
     * Sets the version number for this template.
     *
     * @param string $version Semantic version number in this format: 1.0.0
     *
     * @throws InvalidArgumentException If the version number is invalid.
     */
    public function setVersion(string $version) : void
    {
        if (!preg_match('/^\d+\.\d+\.\d+$/', $version)) {
            throw new InvalidArgumentException(
                'Version number is invalid; ' . $version . ' does not match '
                . 'x.x.x (where x is a number)'
            );
        }

        $this->version = $version;
    }

    /**
     * FlySystem filesystem / MountManager containing the template files, base templates files
     * and destination filesystem.
     *
     * This MountManager has three mounts:
     *
     * - template://, the files of this template
     * - templates://, the base folder containing phpDocumentor's global templates (i.e. `/data/templates`)
     * - destination://, the destination where the template needs to write to
     *
     * By combining this in one mount manager it is easier for writers to copy files between destinations (since
     * MountManager's can copy between filesystems) and for writers to read and write from various locations.
     */
    public function files() : MountManager
    {
        return $this->files;
    }

    /**
     * Returns the version number for this template.
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * Sets the description for this template.
     *
     * @param string $description An unconstrained text field where the user can provide additional information
     *     regarding details of the template.
     */
    public function setDescription(string $description) : void
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this template.
     */
    public function getDescription() : string
    {
        return $this->description;
    }

    /**
     * Sets a transformation at the given offset.
     *
     * @param int|string $offset The offset to place the value at.
     * @param Transformation $value The transformation to add to this template.
     *
     * @throws InvalidArgumentException If an invalid item was received.
     */
    public function offsetSet($offset, $value) : void
    {
        if (!$value instanceof Transformation) {
            throw new InvalidArgumentException(
                '\phpDocumentor\Transformer\Template may only contain items of '
                . 'type \phpDocumentor\Transformer\Transformation'
            );
        }

        $this->transformations[$offset] = $value;
    }

    /**
     * Gets the transformation at the given offset.
     *
     * @param int|string $offset The offset to retrieve from.
     */
    public function offsetGet($offset) : Transformation
    {
        return $this->transformations[$offset];
    }

    /**
     * Offset to unset.
     *
     * @link https://www.php.net/arrayaccess.offsetunset
     *
     * @param int|string $offset Index of item to unset.
     */
    public function offsetUnset($offset) : void
    {
        unset($this->transformations[$offset]);
    }

    /**
     * Whether a offset exists.
     *
     * @link https://www.php.net/arrayaccess.offsetexists
     *
     * @param int|string $offset An offset to check for.
     *
     * @return bool Returns true on success or false on failure.
     */
    public function offsetExists($offset) : bool
    {
        return isset($this->transformations[$offset]);
    }

    /**
     * Count the number of transformations.
     *
     * @link https://www.php.net/countable.count
     *
     * @return int The count as an integer.
     */
    public function count() : int
    {
        return count($this->transformations);
    }

    /**
     * Returns the parameters associated with this template.
     *
     * @return Parameter[]
     */
    public function getParameters() : array
    {
        return $this->parameters;
    }

    /**
     * Sets a new parameter in the collection.
     *
     * @param string|int $key
     */
    public function setParameter($key, Parameter $value) : void
    {
        $this->parameters[$key] = $value;
    }

    /**
     * Pushes the parameters of this template into the transformations.
     */
    public function propagateParameters() : void
    {
        foreach ($this->transformations as $transformation) {
            $transformation->setParameters(array_merge($transformation->getParameters(), $this->getParameters()));
        }
    }

    /**
     * @return ArrayIterator<int|string, Transformation>
     */
    public function getIterator() : ArrayIterator
    {
        return new ArrayIterator($this->transformations);
    }
}
