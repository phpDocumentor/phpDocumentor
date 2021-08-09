<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Event;

use Doctrine\Common\EventArgs;

abstract class BuildEvent extends EventArgs
{
    /** @var string */
    private $directory;

    /** @var string */
    private $targetDirectory;

    public function __construct(
        string $directory,
        string $targetDirectory
    ) {
        $this->directory = $directory;
        $this->targetDirectory = $targetDirectory;
    }

    public function getDirectory(): string
    {
        return $this->directory;
    }

    public function getTargetDirectory(): string
    {
        return $this->targetDirectory;
    }
}
