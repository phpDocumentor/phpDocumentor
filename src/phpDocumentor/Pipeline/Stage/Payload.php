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

namespace phpDocumentor\Pipeline\Stage;

use phpDocumentor\Configuration\VersionSpecification;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;
use phpDocumentor\Dsn;
use phpDocumentor\Path;

class Payload
{
    //phpcs:disable Generic.Files.LineLength.TooLong
    /** @var array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, VersionSpecification>, settings?: array<mixed>, templates?: non-empty-list<string>}} */
    //phpcs:enable Generic.Files.LineLength.TooLong
    private $config;

    /** @var ProjectDescriptorBuilder */
    private $builder;

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, VersionSpecification>, settings?: array<mixed>, templates?: non-empty-list<string>}} $config
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function __construct(array $config, ProjectDescriptorBuilder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @return array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, VersionSpecification>, settings?: array<mixed>, templates?: non-empty-list<string>}}
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }
}
