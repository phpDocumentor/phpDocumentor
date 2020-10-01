<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;
use phpDocumentor\Guides\RestructuredText\Builder;

abstract class BuildEvent extends EventArgs
{
    /** @var Builder */
    private $builder;

    /** @var string */
    private $directory;

    /** @var string */
    private $targetDirectory;

    public function __construct(
        Builder $builder,
        string $directory,
        string $targetDirectory
    ) {
        $this->builder         = $builder;
        $this->directory       = $directory;
        $this->targetDirectory = $targetDirectory;
    }

    public function getBuilder() : Builder
    {
        return $this->builder;
    }

    public function getDirectory() : string
    {
        return $this->directory;
    }

    public function getTargetDirectory() : string
    {
        return $this->targetDirectory;
    }
}
