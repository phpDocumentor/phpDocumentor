<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Directives;

use phpDocumentor\Guides\Nodes\FigureNode;
use phpDocumentor\Guides\Nodes\ImageNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\MarkupLanguageParser;

use function assert;

/**
 * Renders an image, example :
 *
 * .. figure:: image.jpg
 *      :width: 100
 *      :alt: An image
 *
 *      Here is an awesome caption
 */
class Figure extends SubDirective
{
    public function getName(): string
    {
        return 'figure';
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        MarkupLanguageParser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ): ?Node {
        $image = new ImageNode($parser->getEnvironment()->relativeUrl($data));
        $image = $image->withOptions([
            'width' => $options['width'] ?? null,
            'height' => $options['height'] ?? null,
            'alt' => $options['alt'] ?? null,
            'scale' => $options['scale'] ?? null,
            'target' => $options['target'] ?? null,
            'class' => $options['class'] ?? null,
            'name' => $options['name'] ?? null,
        ]);
        assert($image instanceof ImageNode);

        return new FigureNode($image, $document);
    }
}
