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

/**
 * Descriptor representing a function.
 */
class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var Collection $arguments */
    protected $arguments;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setArguments(new Collection());
    }

    /**
     * {@inheritDoc}
     */
    public function setArguments(Collection $arguments)
    {
        $this->arguments = $arguments;
    }

    /**
     * {@inheritDoc}
     */
    public function getArguments()
    {
        return $this->arguments;
    }
}
