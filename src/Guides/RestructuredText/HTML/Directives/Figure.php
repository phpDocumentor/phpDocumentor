<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use Exception;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;
use function sprintf;

/**
 * Renders an image, example :
 *
 * .. figure:: image.jpg
 *      :width: 100
 *      :title: An image
 *
 *      Here is an awesome caption
 */
class Figure extends SubDirective
{
    public function getName() : string
    {
        return 'figure';
    }

    /**
     * @param string[] $options
     */
    public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        $environment = $parser->getEnvironment();

        $url = $environment->relativeUrl($data);

        if ($url === null) {
            throw new Exception(sprintf('Could not get relative url for %s', $data));
        }

        $nodeFactory = $parser->getNodeFactory();

        return $parser->getNodeFactory()->createFigureNode(
            $nodeFactory->createImageNode($url, $options),
            $document
        );
    }
}
