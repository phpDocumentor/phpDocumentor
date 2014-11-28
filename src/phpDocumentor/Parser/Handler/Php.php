<?php

namespace phpDocumentor\Parser\Backend;

use phpDocumentor\Descriptor\Analyzer;
use phpDocumentor\Parser\Backend;

final class Php implements Handler
{
    /** @var string[] */
    private $extensions;

    /** @var Analyzer */
    private $analyzer;

    public function __construct(Analyzer $analyzer)
    {
        $this->analyzer = $analyzer;
    }

    public function boot(\phpDocumentor\Configuration $configuration)
    {
        $this->extensions = $configuration->getParser()->getExtensions();
    }

    public function matches(\SplFileInfo $file)
    {
        return in_array($file->getExtension(), $this->extensions);
    }

    public function parse(\SplFileObject $file)
    {
        $fileDescriptor = $this->analyzer->analyze($file);
        // TODO: Move the FileDescriptor handling from the Analyze method here
    }
}
