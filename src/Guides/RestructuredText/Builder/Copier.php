<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Builder;

use Symfony\Component\Filesystem\Filesystem;
use function basename;
use function dirname;
use function is_dir;

class Copier
{
    /** @var Filesystem */
    private $filesystem;

    /** @var string[][] */
    private $toCopy = [];

    /** @var string[] */
    private $toMkdir = [];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function doCopy(string $sourceDirectory, string $targetDirectory) : void
    {
        foreach ($this->toCopy as $copy) {
            [$source, $destination] = $copy;

            if ($source[0] !== '/') {
                $source = $sourceDirectory . '/' . $source;
            }

            $destination = $targetDirectory . '/' . $destination;

            if (is_dir($source) && is_dir($destination)) {
                $destination = dirname($destination);
            }

            if (is_dir($source)) {
                $this->filesystem->mirror($source, $destination);
            } else {
                $this->filesystem->copy($source, $destination);
            }
        }

        $this->toCopy = [];
    }

    public function doMkdir(string $targetDirectory) : void
    {
        foreach ($this->toMkdir as $mkdir) {
            $dir = $targetDirectory . '/' . $mkdir;

            if (is_dir($dir)) {
                continue;
            }

            $this->filesystem->mkdir($dir, 0755);
        }

        $this->toMkdir = [];
    }

    public function copy(string $source, ?string $destination = null) : void
    {
        if ($destination === null) {
            $destination = basename($source);
        }

        $this->toCopy[] = [$source, $destination];
    }

    public function mkdir(string $directory) : void
    {
        $this->toMkdir[] = $directory;
    }
}
