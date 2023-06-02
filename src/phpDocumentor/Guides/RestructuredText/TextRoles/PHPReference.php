<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\TextRoles;

use phpDocumentor\Guides\Nodes\InlineToken\InlineMarkupToken;
use phpDocumentor\Guides\Nodes\InlineToken\PHPReferenceNode;
use phpDocumentor\Guides\ParserContext;
use phpDocumentor\Guides\RestructuredText\Span\SpanLexer;
use phpDocumentor\Reflection\Fqsen;
use Psr\Log\LoggerInterface;

use function sprintf;
use function strrpos;
use function substr;
use function trim;

final class PHPReference implements TextRole
{
    private SpanLexer $lexer;
    private LoggerInterface $logger;

    public function __construct(
        LoggerInterface $logger,
    ) {
        // Do not inject the $lexer. It contains a state.
        $this->lexer = new SpanLexer();
        $this->logger = $logger;
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
        ParserContext $parserContext,
        string $id,
        string $role,
        string $content,
    ): InlineMarkupToken {
        $text = null;
        $part = '';
        $this->lexer->setInput($content);
        $this->lexer->moveNext();
        $this->lexer->moveNext();
        while ($this->lexer->token !== null) {
            $token = $this->lexer->token;
            switch ($token->type) {
                case SpanLexer::EMBEDED_URL_START:
                    $text = trim($part);
                    $part = '';
                    break;
                case SpanLexer::EMBEDED_URL_END:
                    if ($this->lexer->peek() !== null) {
                        $this->logger->warning(
                            sprintf(
                                'Reference contains unexpected content after closing `>`: "%s"',
                                $content,
                            ),
                            $parserContext->getLoggerInformation(),
                        );
                    }

                    break 2;
                default:
                    $part .= $token->value;
            }

            $this->lexer->moveNext();
        }

        return new PHPReferenceNode(
            id: $id,
            nodeType: substr($role, strrpos($role, ':') + 1),
            fqsen: new Fqsen('\\' . trim($part, '\\')),
            text: $text,
        );
    }
}
