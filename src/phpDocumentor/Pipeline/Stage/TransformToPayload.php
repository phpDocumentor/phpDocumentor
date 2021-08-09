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

final class TransformToPayload
{
    /** @var ProjectDescriptorBuilder */
    private $descriptorBuilder;

    public function __construct(ProjectDescriptorBuilder $descriptorBuilder)
    {
        $this->descriptorBuilder = $descriptorBuilder;
    }

    //phpcs:disable Generic.Files.LineLength.TooLong
    /**
     * @param array{phpdocumentor: array{configVersion: string, title?: string, use-cache?: bool, paths?: array{output: Dsn, cache: Path}, versions?: array<string, VersionSpecification>, settings?: array<mixed>, templates?: non-empty-list<string>}} $configuration
     */
    //phpcs:enable Generic.Files.LineLength.TooLong
    public function __invoke(array $configuration): Payload
    {
        return new Payload($configuration, $this->descriptorBuilder);
    }
}
