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

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Pipeline\PipelineInterface;

final class ParseApiDocumentationSets
{
    public function __construct(private readonly PipelineInterface $parseApiDocumentationSetPipeline)
    {
    }

    public function __invoke(Payload $payload): Payload
    {
        $versions = $payload->getBuilder()->getProjectDescriptor()->getVersions();

        foreach ($versions as $version) {
            foreach ($version->getDocumentationSets()->filter(ApiSetDescriptor::class) as $apiSet) {
                $this->parseApiDocumentationSetPipeline->process(
                    new Parser\ApiSetPayload($payload->getConfig(), $payload->getBuilder(), $version, $apiSet),
                );
            }
        }

        return $payload;
    }
}
