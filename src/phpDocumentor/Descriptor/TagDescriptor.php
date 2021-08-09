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

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\Filter\Filterable;
use phpDocumentor\Reflection\DocBlock\Description;

/**
 * Base class for any tag descriptor and used when a tag has no specific descriptor.
 *
 * @api
 * @package phpDocumentor\AST
 */
class TagDescriptor implements Filterable
{
    /** @var string $name Name of the tag. */
    protected $name;

    /** @var DescriptionDescriptor $description A description line for this tag */
    protected $description;

    /** @var Collection<Validation\Error> A collection of errors found during filtering. */
    protected $errors;

    /**
     * Initializes the tag by setting the name and errors,
     */
    public function __construct(string $name, ?DescriptionDescriptor $description = null)
    {
        $this->setName($name);
        $this->errors = Collection::fromClassString(Validation\Error::class);
        $this->setDescription($description);
    }

    /**
     * Sets the name for this tag.
     */
    protected function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * Returns the name for this tag.
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Sets a description for this tab instance.
     */
    public function setDescription(?DescriptionDescriptor $description): void
    {
        $this->description = $description ?? new DescriptionDescriptor(new Description(''), []);
    }

    /**
     * Returns the description for this tag,
     */
    public function getDescription(): DescriptionDescriptor
    {
        return $this->description;
    }

    public function __toString(): string
    {
        return (string) $this->description;
    }

    /**
     * Sets a list of errors found on the usage of this tag.
     *
     * @param Collection<Validation\Error> $errors
     */
    public function setErrors(Collection $errors): void
    {
        $this->errors = $errors;
    }

    /**
     * Returns all errors associated with this tag.
     *
     * @return Collection<Validation\Error>
     */
    public function getErrors(): Collection
    {
        return $this->errors;
    }
}
