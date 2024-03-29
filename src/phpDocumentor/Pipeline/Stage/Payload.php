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

use phpDocumentor\Configuration\Configuration;
use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

/** @psalm-import-type ConfigurationMap from Configuration */
class Payload
{
    /** @param ConfigurationMap $config */
    public function __construct(private readonly array $config, private readonly ProjectDescriptorBuilder $builder)
    {
    }

    /** @return ConfigurationMap */
    public function getConfig(): array
    {
        return $this->config;
    }

    public function getBuilder(): ProjectDescriptorBuilder
    {
        return $this->builder;
    }
}
