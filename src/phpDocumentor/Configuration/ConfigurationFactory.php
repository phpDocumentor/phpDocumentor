<?php

namespace phpDocumentor\Configuration;

use phpDocumentor\Uri;

final class ConfigurationFactory
{
    /**
     * @var Uri
     */
    private $uri;

    function __construct(Uri $uri)
    {
        $this->uri = $uri;
    }
}
