<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use phpDocumentor\Guides\RestructuredText\Meta\Metas;
use phpDocumentor\Guides\RestructuredText\Nodes\DocumentNode;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use function dirname;
use function is_dir;
use function sprintf;

class Documents
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Metas */
    private $metas;

    /** @var DocumentNode[] */
    private $documents = [];

    public function __construct(
        Filesystem $filesystem,
        Metas $metas
    ) {
        $this->filesystem = $filesystem;
        $this->metas      = $metas;
    }

    /**
     * @return DocumentNode[]
     */
    public function getAll() : array
    {
        return $this->documents;
    }

    public function hasDocument(string $file) : bool
    {
        return isset($this->documents[$file]);
    }

    public function addDocument(string $file, DocumentNode $document) : void
    {
        $this->documents[$file] = $document;
    }

    public function render(string $targetDirectory) : void
    {
        foreach ($this->documents as $file => $document) {
            $target = $this->getTargetOf($targetDirectory, $file);

            $directory = dirname($target);

            if (! is_dir($directory)) {
                $this->filesystem->mkdir($directory, 0755);
            }

            $this->filesystem->dumpFile($target, $document->renderDocument());
        }
    }

    private function getTargetOf(string $targetDirectory, string $file) : string
    {
        $metaEntry = $this->metas->get($file);

        if ($metaEntry === null) {
            throw new InvalidArgumentException(sprintf('Could not find target file for %s', $file));
        }

        return $targetDirectory . '/' . $metaEntry->getUrl();
    }
}
