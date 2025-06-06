<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor;

use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyHookInterface;

final class PropertyHookDescriptor implements Descriptor, PropertyHookInterface
{
    use Traits\HasName;
    use Traits\HasVisibility;
    use Traits\HasSummary;
    use Traits\HasDescription;
    use Traits\HasTags;
    use Traits\HasAttributes;
    use Traits\HasMetadata;

    /** @var Collection<ArgumentInterface> */
    private Collection $arguments;

    public function __construct()
    {
        $this->visibility = new ValueObjects\Visibility(ValueObjects\VisibilityModifier::PUBLIC);
        $this->arguments = Collection::fromInterfaceString(ArgumentInterface::class);
    }

    public function addArgument(string $name, ArgumentInterface $argument): void
    {
        $this->arguments->set($name, $argument);
    }

    /** @return Collection<ArgumentInterface> */
    public function getArguments(): Collection
    {
        return $this->arguments;
    }
}
