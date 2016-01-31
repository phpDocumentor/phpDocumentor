<?php

namespace phpDocumentor\Renderer;

use League\Flysystem\Filesystem;
use phpDocumentor\DomainModel\Documentation;
use phpDocumentor\DomainModel\Path;

class RenderPass
{
    /** @var Filesystem */
    private $filesystem;

    /** @var Path */
    private $destination;
    /**
     * @var Documentation
     */
    private $documentation;

    /**
     * @param Filesystem  $filesystem
     * @param Path        $destination
     */
    public function __construct(Filesystem $filesystem, Path $destination, Documentation $documentation)
    {
        $this->destination = $destination;
        $this->filesystem  = $filesystem;
        $this->documentation = $documentation;
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

    /**
     * @return Documentation
     */
    public function getDocumentation()
    {
        return $this->documentation;
    }
}
