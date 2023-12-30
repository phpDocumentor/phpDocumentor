<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\TextRoles;

use phpDocumentor\Guides\Nodes\Inline\InlineNode;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\RestructuredText\Parser\DocumentParserContext;
use phpDocumentor\Guides\RestructuredText\Parser\InlineLexer;
use phpDocumentor\Reflection\Fqsen;
use Psr\Log\LoggerInterface;

use function sprintf;
use function strrpos;
use function substr;
use function trim;

final class PHPReference implements TextRole
{
    private readonly InlineLexer $lexer;

    public function __construct(
        private readonly LoggerInterface $logger,
    ) {
        // Do not inject the $lexer. It contains a state.
        $this->lexer = new InlineLexer();
    }

    public function getName(): string
    {
        return 'class';
    }

    public function getAliases(): array
    {
        return [
            'namespace',
            'interface',
            'trait',
            'enum',
            'method',
            'property',
            'function',
        ];
    }

    public function processNode(
        DocumentParserContext $documentParserContext,
        string $role,
        string $content,
        string $rawContent,
    ): InlineNode {
        $text = null;
        $part = '';
        $this->lexer->setInput($rawContent);
        $this->lexer->moveNext();
        $this->lexer->moveNext();
        while ($this->lexer->token !== null) {
            $token = $this->lexer->token;
            switch ($token->value) {
                case '<':
                    $text = trim($part);
                    $part = '';
                    break;
                case '>':
                    if ($this->lexer->peek() !== null) {
                        $this->logger->warning(
                            sprintf(
                                'Reference contains unexpected content after closing `>`: "%s"',
                                $content,
                            ),
                            $documentParserContext->getContext()->getLoggerInformation(),
                        );
                    }

                    break 2;
                default:
                    $part .= $token->value;
            }

            $this->lexer->moveNext();
        }

        return new PHPReferenceNode(
            nodeType: substr($role, strrpos($role, ':') + 1),
            fqsen: new Fqsen('\\' . trim($part, '\\')),
            text: $text,
        );
    }
}
