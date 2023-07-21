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
final class TransformToPayload
{
    public function __construct(private readonly ProjectDescriptorBuilder $descriptorBuilder)
    {
    }

    /** @param ConfigurationMap $configuration */
    public function __invoke(array $configuration): Payload
    {
        return new Payload($configuration, $this->descriptorBuilder);
    }
}
