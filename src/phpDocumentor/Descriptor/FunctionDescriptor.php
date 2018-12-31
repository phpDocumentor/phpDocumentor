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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Type;

/**
 * Descriptor representing a function.
 */
class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var Collection $arguments */
    protected $arguments;

    /** @var Type */
    private $returnType;

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

    /**
     * {@inheritDoc}
     */
    public function getResponse(): ReturnDescriptor
    {
        $definedReturn = new ReturnDescriptor('return');
        $definedReturn->setType($this->returnType);

        /** @var Collection|null $returnTags */
        $returnTags = $this->getTags()->get('return');

        if ($returnTags instanceof Collection && $returnTags->count() > 0) {
            /** @var ReturnDescriptor $returnTag */
            return current($returnTags->getAll());
        }

        return $definedReturn;
    }

    /**
     * Sets return type of this method.
     */
    public function setReturnType(Type $returnType)
    {
        $this->returnType = $returnType;
    }
}
