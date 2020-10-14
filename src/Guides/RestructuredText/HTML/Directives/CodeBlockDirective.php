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

use Exception;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Nodes\Node;
use phpDocumentor\Guides\Renderers\Html\CodeNodeRenderer;
use phpDocumentor\Guides\RestructuredText\Directives\Directive;
use phpDocumentor\Guides\RestructuredText\Parser;
use function sprintf;

class CodeBlockDirective extends Directive
{
    public function getName() : string
    {
        return 'code-block';
    }

    public function process(Parser $parser, ?Node $node, string $variable, string $data, array $options) : void
    {
        if (!$node instanceof CodeNode) {
            return;
        }

        if (!CodeNodeRenderer::isLanguageSupported($data)) {
            throw new Exception(sprintf('Unsupported code block language "%s"', $data));
        }

        $node->setLanguage($data);

        if ($variable !== '') {
            $environment = $parser->getEnvironment();
            $environment->setVariable($variable, $node);
        } else {
            $document = $parser->getDocument();
            $document->addNode($node);
        }
    }

    public function wantCode() : bool
    {
        return true;
    }
}
