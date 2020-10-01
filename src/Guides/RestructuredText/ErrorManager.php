<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText;

use Exception;

class ErrorManager
{
    /** @var Configuration */
    private $configuration;

    /** @var string[] */
    private $errors = [];

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function error(string $message) : void
    {
        $this->errors[] = $message;

        if ($this->configuration->isAbortOnError()) {
            throw new Exception($message);
        }

        echo '/!\\ ' . $message . "\n";
    }

    /**
     * @return string[]
     */
    public function getErrors() : array
    {
        return $this->errors;
    }
}
