<?php

namespace phpDocumentor\Plugin\Twig;

use \phpDocumentor\Transformer\Transformation;

interface ExtensionInterface
{
    public function __construct(
        \SimpleXMLElement $structure, Transformation $transformation
    );
}
