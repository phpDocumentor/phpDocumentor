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

use phpDocumentor\Configuration\ApiSpecification;
use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Reflection\File;

/**
 * @psalm-import-type ConfigurationMap from Configuration
 */
final class ApiSetPayload
{
    private array $configuration;
    private ProjectDescriptorBuilder $builder;
    private ApiSpecification $specification;

    /** @var File[] */
    private array $files;

    /**
     * @param ConfigurationMap $config
     * @param File[] $files
     */
    public function __construct(
        array $configuration,
        ProjectDescriptorBuilder $builder,
        ApiSpecification $specification,
        array $files = []
    ) {
        $this->configuration = $configuration;
        $this->builder = $builder;
        $this->specification = $specification;

        $this->files = $files;
    }

    /**
     * @return ConfigurationMap
     */
    public function getConfiguration(): array
    {
        return $this->configuration;
    }

    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }

    public function getSpecification(): ApiSpecification
    {
        return $this->specification;
    }

    /**
     * @param array<File> $files
     */
    public function withFiles(array $files): self
    {
        return new static(
            $this->getConfiguration(),
            $this->getBuilder(),
            $this->getSpecification(),
            array_merge($this->getFiles(), $files)
        );
    }

    /**
     * @return File[]
     */
    public function getFiles(): array
    {
        return $this->files;
    }
}
