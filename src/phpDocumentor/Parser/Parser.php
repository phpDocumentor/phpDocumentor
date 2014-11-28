<?php

namespace phpDocumentor\Parser;

use phpDocumentor\Fileset\Collection;
use Psr\Log\LoggerAwareTrait;

final class Parser
{
    /** @var Handler[] */
    private $handlers;

    public function registerHandler(Handler $handler)
    {
        $this->handlers[] = $handler;
    }

    public function parse(Collection $files, \phpDocumentor\Configuration $configuration)
    {
        $this->bootHandlers($configuration);
        $this->parseFiles($files);
    }

    /**
     * @param Configuration $configuration
     */
    private function bootHandlers(\phpDocumentor\Configuration $configuration)
    {
        foreach ($this->handlers as $handler) {
            $handler->boot($configuration);
        }
    }

    /**
     * @param Collection $files
     */
    private function parseFiles(Collection $files)
    {
        /** @var \SplFileInfo $file */
        foreach ($files as $file) {
            $this->parseFile($file);
        }
    }

    /**
     * @param $file
     */
    private function parseFile($file)
    {
        foreach ($this->handlers as $handler) {
            if ($handler->matches($file)) {
                $handler->parse($file->openFile());
                continue;
            }
        }
    }
}
