<?php

namespace phpDocumentor\Application\Stage\Parser;

use phpDocumentor\Application\Stage\Payload as ApplicationPayload;

final class TransformToParserPayload
{
    public function __invoke(ApplicationPayload $payload)
    {
        return new Payload($payload->getConfig(), $payload->getBuilder());
    }
}
