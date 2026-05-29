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

namespace phpDocumentor\Compiler;

use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\Interfaces\ApiDocumentationSet;
use phpDocumentor\Descriptor\Interfaces\VersionInterface;
use phpDocumentor\Reflection\Fqsen;

final class DescriptorRepository
{
    private VersionInterface $versionDescriptor;

    public function setVersionDescriptor(VersionInterface $versionDescriptor): void
    {
        $this->versionDescriptor = $versionDescriptor;
    }

    public function getVersionDescriptor(): VersionInterface
    {
        return $this->versionDescriptor;
    }

    public function findDescriptorByFqsen(Fqsen $fqsen): Descriptor|null
    {
        $apis = $this->versionDescriptor->getDocumentationSets()->filter(ApiDocumentationSet::class);

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
        $apis = $this->versionDescriptor->getDocumentationSets()->filter(ApiDocumentationSet::class);

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
