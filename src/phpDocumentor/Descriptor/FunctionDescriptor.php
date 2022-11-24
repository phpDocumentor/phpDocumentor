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

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Tag\ReturnDescriptor;
use phpDocumentor\Reflection\Type;

use function current;

/**
 * Descriptor representing a function.
 *
 * @api
 * @package phpDocumentor\AST
 */
class FunctionDescriptor extends DescriptorAbstract implements Interfaces\FunctionInterface
{
    /** @var Collection<ArgumentInterface> $arguments */
    protected Collection $arguments;

    private Type $returnType;
    private bool $hasReturnByReference = false;

    /**
     * Initializes the all properties representing a collection with a new Collection object.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setArguments(new Collection());
    }

    public function setArguments(Collection $arguments): void
    {
        $this->arguments = $arguments;
    }

    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function getResponse(): ReturnDescriptor
    {
        $definedReturn = new ReturnDescriptor('return');
        $definedReturn->setType($this->returnType);

        /** @var Collection<ReturnDescriptor> $returnTags */
        $returnTags = $this->getTags()->fetch('return', new Collection())->filter(ReturnDescriptor::class);

        if ($returnTags instanceof Collection && $returnTags->count() > 0) {
            return current($returnTags->getAll());
        }

        return $definedReturn;
    }

    /**
     * Sets return type of this method.
     */
    public function setReturnType(Type $returnType): void
    {
        $this->returnType = $returnType;
    }

    public function setHasReturnByReference(bool $hasReturnByReference): void
    {
        $this->hasReturnByReference = $hasReturnByReference;
    }

    public function getHasReturnByReference(): bool
    {
        return $this->hasReturnByReference;
    }
}
