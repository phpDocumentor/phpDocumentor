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

final class TransformToPayload
{
    /** @var ProjectDescriptorBuilder */
    private $descriptorBuilder;

    public function __construct(ProjectDescriptorBuilder $descriptorBuilder)
    {
        $this->descriptorBuilder = $descriptorBuilder;
    }

    /**
     * @param array<string, string> $configuration
     */
    public function __invoke(array $configuration) : Payload
    {
        return new Payload($configuration, $this->descriptorBuilder);
    }
}
