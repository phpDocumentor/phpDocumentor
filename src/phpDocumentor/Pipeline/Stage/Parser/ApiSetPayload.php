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

namespace phpDocumentor\Pipeline\Stage\Parser;

use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Reflection\File;

use function array_merge;

/** @psalm-import-type ConfigurationMap from Configuration */
final class ApiSetPayload
{
    /**
     * @param ConfigurationMap $configuration
     * @param File[] $files
     */
    public function __construct(
        private readonly array $configuration,
        private readonly ProjectDescriptorBuilder $builder,
        private readonly VersionDescriptor $version,
        private readonly ApiSetDescriptor $apiSet,
        private readonly array $files = [],
    ) {
    }

    /** @return ConfigurationMap */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    public function getVersion(): VersionDescriptor
    {
        return $this->version;
    }

    public function getApiSet(): ApiSetDescriptor
    {
        return $this->apiSet;
    }

    /** @param array<File> $files */
    public function withFiles(array $files): self
    {
        return new static(
            $this->getConfiguration(),
            $this->getBuilder(),
            $this->getVersion(),
            $this->getApiSet(),
            array_merge($this->getFiles(), $files),
        );
    }

    /** @return File[] */
    public function getFiles(): array
    {
        return $this->files;
    }
}
