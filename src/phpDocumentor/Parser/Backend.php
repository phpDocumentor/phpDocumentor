<?php

namespace phpDocumentor\Parser;

interface Backend
{
    public function matches(\SplFileInfo $file);
    public function boot(\phpDocumentor\Configuration $configuration);
    public function parse(\SplFileObject $file);
}
