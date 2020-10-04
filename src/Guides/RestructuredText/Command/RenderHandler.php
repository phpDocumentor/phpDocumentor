<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Command;

use phpDocumentor\Guides\RestructuredText\Builder\Copier;
use phpDocumentor\Guides\RestructuredText\Builder\Documents;
use Symfony\Component\Filesystem\Filesystem;

final class RenderHandler
{
    /** @var Copier */
    private $copier;

    /** @var Documents */
    private $documents;

    public function __construct(Documents $documents, Filesystem $filesystem)
    {
        $this->copier = new Copier($filesystem);
        $this->documents = $documents;
    }

    public function handle(RenderCommand $command) : void
    {
        $directory = $command->getDirectory();
        $targetDirectory = $command->getOutputDirectory();

        $this->documents->render($targetDirectory);

        $this->copier->doMkdir($targetDirectory);
        $this->copier->doCopy($directory, $targetDirectory);
    }
}
