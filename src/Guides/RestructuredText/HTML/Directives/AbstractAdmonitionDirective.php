<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

namespace phpDocumentor\Guides\RestructuredText\HTML\Directives;

use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\RestructuredText\Directives\SubDirective;
use phpDocumentor\Guides\RestructuredText\Parser;

abstract class AbstractAdmonitionDirective extends SubDirective
{
    /** @var string */
    private $name;

    /** @var string */
    private $text;

    public function __construct(string $name, string $text)
    {
        $this->name = $name;
        $this->text = $text;
    }

    final public function processSub(
        Parser $parser,
        ?Node $document,
        string $variable,
        string $data,
        array $options
    ) : ?Node {
        $environment = $document->getEnvironment();
        $wrapperDiv = function () use ($environment, $options) {
            return $environment->getRenderer()->render(
                'directives/admonition.html.twig',
                [
                    'name' => $this->name,
                    'text' => $this->text,
                    'class' => $options['class'] ?? null,
                ]
            );
        };

        return $parser->getNodeFactory()->createRawNode($wrapperDiv);
    }

    final public function getName() : string
    {
        return $this->name;
    }
}
