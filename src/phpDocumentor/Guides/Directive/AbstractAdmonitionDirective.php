<?php

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Directive;

use Doctrine\RST\Directives\SubDirective;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Parser;

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

    final public function processSub(Parser $parser, ?Node $document, string $variable, string $data, array $options): ?Node
    {
        $wrapperDiv = $parser->renderTemplate(
            'directives/admonition.html.twig',
            [
                'name' => $this->name,
                'text' => $this->text,
                'class' => isset($options['class']) ? $options['class'] : null,
            ]
        );

        return $parser->getNodeFactory()->createWrapperNode($document, $wrapperDiv, '</div></div>');
    }

    final public function getName(): string
    {
        return $this->name;
    }
}
