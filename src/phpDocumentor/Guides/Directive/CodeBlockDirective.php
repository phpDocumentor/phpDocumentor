<?php

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Directive;

use Doctrine\RST\Directives\Directive;
use Doctrine\RST\Nodes\CodeNode;
use Doctrine\RST\Nodes\Node;
use Doctrine\RST\Parser;
use phpDocumentor\Guides\Renderers\CodeNodeRenderer;

class CodeBlockDirective extends Directive
{
    public function getName(): string
    {
        return 'code-block';
    }

    public function process(Parser $parser, ?Node $node, string $variable, string $data, array $options): void
    {
        if (!$node instanceof CodeNode) {
            return;
        }

        if (!CodeNodeRenderer::isLanguageSupported($data)) {
            throw new \Exception(sprintf('Unsupported code block language "%s"', $data));
        }

        $node->setLanguage($data);

        if ('' !== $variable) {
            $environment = $parser->getEnvironment();
            $environment->setVariable($variable, $node);
        } else {
            $document = $parser->getDocument();
            $document->addNode($node);
        }
    }

    public function wantCode(): bool
    {
        return true;
    }
}
