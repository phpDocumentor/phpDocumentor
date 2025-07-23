<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Traits\HasDescription;
use phpDocumentor\Descriptor\Traits\HasFqsen;
use phpDocumentor\Descriptor\Traits\HasName;
use phpDocumentor\Reflection\Fqsen;
use Stringable;

final class AttributeDescriptor implements Descriptor, Stringable, AttributeInterface
{
    use HasName;
    use HasDescription;
    use HasFqsen;

    /** @var Collection<ValueObjects\CallArgument> */
    private Collection $arguments;
    private ClassInterface|null $attributeClass = null;

    public function __construct()
    {
        $this->arguments = Collection::fromClassString(ValueObjects\CallArgument::class);
    }

    public function getAttribute(): ClassInterface|Fqsen|null
    {
        if ($this->attributeClass !== null) {
            return $this->attributeClass;
        }

        return $this->fqsen;
    }

    public function setAttribute(ClassInterface|null $attributeClass): void
    {
        $this->attributeClass = $attributeClass;
    }

    public function __toString(): string
    {
        return $this->getName();
    }

    public function addArgument(ValueObjects\CallArgument $argument): void
    {
        $this->arguments->add($argument);
    }

    /** @return Collection<ValueObjects\CallArgument> */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }

    public function getArgument(string $name): ValueObjects\CallArgument|null
    {
        foreach ($this->arguments as $argument) {
            if ($argument->getName() === $name) {
                return $argument;
            }
        }

        return null;
    }

    public function hasArguments(): bool
    {
        return $this->arguments->count() > 0;
    }
}
