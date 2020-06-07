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

use phpDocumentor\Descriptor\ProjectDescriptorBuilder;

class Payload
{
    /** @var array<string, string|array<mixed>> */
    private $config;

    /** @var ProjectDescriptorBuilder */
    private $builder;

    /**
     * @param array<string, string|array<mixed>> $config
     */
    public function __construct(array $config, ProjectDescriptorBuilder $builder)
    {
        $this->config = $config;
        $this->builder = $builder;
    }

    /**
     * @return array<string, string|array<mixed>>
     */
    public function getConfig() : array
    {
        return $this->config;
    }

    public function getBuilder() : ProjectDescriptorBuilder
    {
        return $this->builder;
    }
}
