<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Exception;

class ErrorManager
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function error(string $message) : void
    {
        if ($this->configuration->isAbortOnError()) {
            throw new Exception($message);
        }

        echo '/!\\ ' . $message . "\n";
    }
}
