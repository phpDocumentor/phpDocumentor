<?php

declare(strict_types=1);

namespace phpDocumentor\Pipeline\Stage;

use phpDocumentor\FlowService\ServiceProvider;

final class Parse
{
    /**
     * @var ServiceProvider
     */
    private $provider;

    public function __construct(ServiceProvider $provider)
    {
        $this->provider = $provider;
    }

    public function __invoke(Payload $payload)
    {
        foreach ($payload->getBuilder()->getProjectDescriptor()->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                $processor = $this->provider->get($documentationSet);
                $processor->operate($documentationSet);
            }
        }

        return $payload;
    }
}
