<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use phpDocumentor\Guides\Files;

final class PreBuildParseEvent extends BuildEvent
{
    public const PRE_BUILD_PARSE = 'preBuildParse';

    /** @var Files */
    private $parseQueue;

    public function __construct(string $directory, string $targetDirectory, Files $parseQueue)
    {
        parent::__construct($directory, $targetDirectory);
        $this->parseQueue = $parseQueue;
    }

    public function getParseQueue() : Files
    {
        return $this->parseQueue;
    }
}
