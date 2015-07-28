<?php

namespace phpDocumentor\Renderer;

use League\Flysystem\Filesystem;
use phpDocumentor\Path;

class RenderPass
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Path */
    private $destination;

    /**
     * @param Filesystem  $filesystem
     * @param Path        $destination
     */
    public function __construct(Filesystem $filesystem, Path $destination)
    {
        $this->destination = $destination;
        $this->filesystem  = $filesystem;
    }

    /**
     * @return Filesystem
     */
    public function getFilesystem()
    {
        return $this->filesystem;
    }

    /**
     * @return Path
     */
    public function getDestination()
    {
        return $this->destination;
    }
}
