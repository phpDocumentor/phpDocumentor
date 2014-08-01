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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Filter\Filterable;

/**
 * Base class for any tag descriptor and used when a tag has no specific descriptor.
 */
class TagDescriptor implements Filterable
{
    /** @var string $name Name of the tag. */
    protected $name;

    /** @var string $description A description line for this tag */
    protected $description = '';

    /** @var Collection A collection of errors found during filtering. */
    protected $errors;

    /**
     * Initializes the tag by setting the name and errors,
     *
     * @param string $name
     */
    public function __construct($name)
    {
        $this->setName($name);
        $this->errors = new Collection();
    }

    /**
     * Sets the name for this tag.
     *
     * @param string $name
     *
     * @return void
     */
    protected function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the name for this tag.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets a description for this tab instance.
     *
     * @param string $description
     *
     * @return void
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * Returns the description for this tag,
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Sets a list of errors found on the usage of this tag.
     *
     * @param Collection $errors
     *
     * @return void
     */
    public function setErrors(Collection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors associated with this tag.
     *
     * @return Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
