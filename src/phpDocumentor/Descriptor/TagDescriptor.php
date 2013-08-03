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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Filter\Filterable;

class TagDescriptor implements Filterable
{
    protected $name;
    protected $description;
    protected $errors;

    public function __construct($name)
    {
        $this->setName($name);
        $this->errors = new Collection();
    }

    protected function setName($name)
    {
        $this->name = $name;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = $description;
    }

    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param Collection $errors
     */
    public function setErrors(Collection $errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return Collection
     */
    public function getErrors()
    {
        return $this->errors;
    }
}
