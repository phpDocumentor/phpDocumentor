<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\ImageNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;
use phpDocumentor\Guides\UrlGenerator;

/**
 * Renders an image, example :
 *
 * .. image:: image.jpg
 *      :width: 100
 *      :title: An image
 */
class Image extends Directive
{
    /** @var UrlGenerator */
    private $urlGenerator;

    public function __construct(UrlGenerator $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }

    public function getName(): string
    {
        return 'image';
    }

    /**
     * @param string[] $options
     */
    public function processNode(
        MarkupLanguageParser $parser,
        string $variable,
        string $data,
        array $options
    ): Node {
        return new ImageNode($this->urlGenerator->relativeUrl($data));
    }
}
