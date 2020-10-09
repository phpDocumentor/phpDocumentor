<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

final class PreBuildParseEvent extends BuildEvent
{
    public const PRE_BUILD_PARSE = 'preBuildParse';

    /** @var \phpDocumentor\Guides\Files */
    private $parseQueue;

    public function __construct(Builder $builder, string $directory, string $targetDirectory, \phpDocumentor\Guides\Files $parseQueue)
    {
        parent::__construct($builder, $directory, $targetDirectory);
        $this->parseQueue = $parseQueue;
    }

    public function getParseQueue() : \phpDocumentor\Guides\Files
    {
        return $this->parseQueue;
    }
}
