<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Application\Stage\Parser;

use phpDocumentor\Application\Stage\Payload as ApplicationPayload;

final class TransformToParserPayload
{
    public function __invoke(ApplicationPayload $payload) : Payload
    {
        return new Payload($payload->getConfig(), $payload->getBuilder());
    }
}
