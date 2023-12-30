<?php

declare(strict_types=1);

namespace phpDocumentor\Compiler;

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Reflection\Fqsen;

final class DescriptorRepository
{
    private VersionDescriptor $versionDescriptor;

    public function setVersionDescriptor(VersionDescriptor $versionDescriptor): void
    {
        $this->versionDescriptor = $versionDescriptor;
    }

    public function getVersionDescriptor(): VersionDescriptor
    {
        return $this->versionDescriptor;
    }

    public function findDescriptorByFqsen(Fqsen $fqsen): Descriptor|null
    {
        $apis = $this->versionDescriptor->getDocumentationSets()->filter(ApiSetDescriptor::class);

        foreach ($apis as $api) {
            $descriptor = $api->findElement($fqsen);
            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }

    public function findDescriptorByTypeAndFqsen(string $type, Fqsen $fqsen): Descriptor|null
    {
        $apis = $this->versionDescriptor->getDocumentationSets()->filter(ApiSetDescriptor::class);

        foreach ($apis as $api) {
            $descriptor = match ($type) {
                'class' => $api->getIndex('classes')->fetch((string) $fqsen),
                'method', 'property' => $api->getIndex('elements')->fetch((string) $fqsen),
                default => $api->getIndex($type . 's')->fetch((string) $fqsen)
            };

            if ($descriptor !== null) {
                return $descriptor;
            }
        }

        return null;
    }
}
