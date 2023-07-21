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

namespace phpDocumentor\Pipeline\Stage\Cache;

use phpDocumentor\Descriptor\Cache\ProjectDescriptorMapper;
use phpDocumentor\Pipeline\Stage\Parser\ApiSetPayload;

final class GarbageCollectCache
{
    public function __construct(private readonly ProjectDescriptorMapper $descriptorMapper)
    {
    }

    public function __invoke(ApiSetPayload $payload): ApiSetPayload
    {
        $this->descriptorMapper->garbageCollect(
            $payload->getVersion(),
            $payload->getApiSet(),
            $payload->getFiles(),
        );

        return $payload;
    }
}
