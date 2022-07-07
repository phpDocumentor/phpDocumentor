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
    /**
     * @var array{
     *     phpdocumentor: array{
     *         configVersion: string,
     *         title?: string,
     *         use-cache?: bool,
     *         paths?: array{
     *             output: Dsn,
     *             cache: Path
     *         },
     *         versions?: array<string, VersionSpecification>,
     *         settings?: array<mixed>,
     *         templates?: non-empty-list<string>
     *     }
     * }
     */
    private $config;

    /** @var ProjectDescriptorBuilder */
    private $builder;

    // @phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName
    /**
     * @param array{
     *     phpdocumentor: array{
     *         configVersion: string,
     *         title?: string,
     *         use-cache?: bool,
     *         paths?: array{
     *             output: Dsn,
     *             cache: Path
     *         },
     *         versions?: array<string, VersionSpecification>,
     *         settings?: array<mixed>,
     *         templates?: non-empty-list<string>
     *     }
     * } $config
     */
    // @phpcs:enable Squiz.Commenting.FunctionComment.MissingParamName
    public function __construct(array $config, ProjectDescriptorBuilder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    // @phpcs:disable Squiz.Commenting.FunctionComment.MissingParamName
    /**
     * @return array{
     *     phpdocumentor: array{
     *         configVersion: string,
     *         title?: string,
     *         use-cache?: bool,
     *         paths?: array{
     *             output: Dsn,
     *             cache: Path
     *         },
     *         versions?: array<string, VersionSpecification>,
     *         settings?: array<mixed>,
     *         templates?: non-empty-list<string>
     *     }
     * }
     */
    // @phpcs:enable Squiz.Commenting.FunctionComment.MissingParamName
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }
}
