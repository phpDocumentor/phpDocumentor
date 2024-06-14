<?php

declare(strict_types=1);

namespace phpDocumentor\Descriptor\ValueObjects;

use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;

final class Deprecation
{
    public function __construct(private DescriptionDescriptor $description, private string|null $version = null)
    {
    }

    public function getDescription(): DescriptionDescriptor
    {
        return $this->description;
    }

    public function getVersion(): string|null
    {
        return $this->version;
    }
}
