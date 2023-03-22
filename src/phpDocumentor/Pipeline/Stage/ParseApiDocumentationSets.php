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

use League\Pipeline\Pipeline;
use phpDocumentor\Configuration\VersionSpecification;

final class ParseApiDocumentationSets
{
    private Pipeline $parseApiDocumentationSetPipeline;

    public function __construct(Pipeline $parseApiDocumentationSetPipeline)
    {
        $this->parseApiDocumentationSetPipeline = $parseApiDocumentationSetPipeline;
    }

    public function __invoke(Payload $payload): Payload
    {
        /** @var VersionSpecification[] $versions */
        $versions = $payload->getConfig()['phpdocumentor']['versions'];

        foreach ($versions as $version) {
            foreach ($version->getApi() as $apiSpecification) {
                $this->parseApiDocumentationSetPipeline->process(
                    new Parser\ApiSetPayload($payload->getConfig(), $payload->getBuilder(), $apiSpecification)
                );
            }
        }

        return $payload;
    }
}
