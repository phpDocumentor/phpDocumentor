<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\RestructuredText\Span;

use phpDocumentor\Guides\Environment;

use function mt_rand;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function sha1;
use function str_replace;
use function time;
use function trim;

class SpanParser
{
    /** @var Environment */
    private $environment;

    /** @var int */
    private $tokenId;

    /** @var string */
    private $prefix;

    /** @var SpanToken[] */
    private $tokens = [];

    /** @var SpanLexer */
    private $lexer;

    public function __construct(Environment $environment)
    {
        $this->lexer = new SpanLexer();
        $this->environment = $environment;
        $this->tokenId = 0;
        $this->prefix = mt_rand() . '|' . time();
    }

    public function process(string $span): string
    {
        $span = $this->replaceLiterals($span);
        $span = $this->replaceReferences($span);

        $this->lexer->setInput($span);
        $this->lexer->moveNext();
        $this->lexer->moveNext();

        $result = $this->parseTokens();
        $result = $this->replaceStandaloneHyperlinks($result);

        return $this->replaceStandaloneEmailAddresses($result);
    }

    /**
     * @return SpanToken[]
     */
    public function getTokens(): array
    {
        return $this->tokens;
    }

    /**
     * @param mixed[] $tokenData
     */
    private function addToken(string $type, string $id, array $tokenData): void
    {
        $this->tokens[$id] = new SpanToken($type, $id, $tokenData);
    }

    private function replaceLiterals(string $span): string
    {
        return preg_replace_callback(
            '/``(.+)``(?!`)/mUsi',
            function (array $match) {
                $id = $this->generateId();

                $this->addToken(
                    SpanToken::TYPE_LITERAL,
                    $id,
                    [
                        'type' => 'literal',
                        'text' => $match[1],
                    ]
                );

                return $id;
            },
            $span
        );
    }

    private function createNamedReference(string $link, ?string $url = null): string
    {
        // the link may have a new line in it so we need to strip it
        // before setting the link and adding a token to be replaced
        $link = str_replace("\n", ' ', $link);
        $link = trim(preg_replace('/\s+/', ' ', $link));

        $id = $this->generateId();
        $this->addToken(
            SpanToken::TYPE_LINK,
            $id,
            [
                'type' => SpanToken::TYPE_LINK,
                'link' => $link,
                'url' => $url ?? '',
            ]
        );

        if ($url !== null) {
            $this->environment->setLink($link, $url);
        }

        return $id;
    }

    private function createAnonymousReference(string $link): string
    {
        $this->environment->resetAnonymousStack();
        $id = $this->createNamedReference($link);
        $this->environment->pushAnonymous($link);

        return $id;
    }

    private function replaceReferences(string $span): string
    {
        return preg_replace_callback(
            '/:([a-z0-9]+):`(.+)`/mUsi',
            function ($match) {
                $section = $match[1];

                $url = $match[2];
                $id = $this->generateId();
                $anchor = null;

                $text = null;
                if (preg_match('/^(.+)<(.+)>$/mUsi', $url, $match) > 0) {
                    $text = $match[1];
                    $url = $match[2];
                }

                if (preg_match('/^(.+)#(.+)$/mUsi', $url, $match) > 0) {
                    $url = $match[1];
                    $anchor = $match[2];
                }

                $this->addToken(
                    SpanToken::TYPE_REFERENCE,
                    $id,
                    [
                        'section' => $section,
                        'url' => $url,
                        'text' => $text,
                        'anchor' => $anchor,
                    ]
                );

                $this->environment->found($section, $url);

                return $id;
            },
            $span
        );
    }

    private function replaceStandaloneHyperlinks(string $span): string
    {
        // Replace standalone hyperlinks using a modified version of @gruber's
        // "Liberal Regex Pattern for all URLs", https://gist.github.com/gruber/249502
        $absoluteUriPattern = '#(?i)\b((?:[a-z][\w\-+.]+:(?:/{1,3}|[a-z0-9%]))('
            . '?:[^\s()<>]+|\(([^\s()<>]+|(\([^\s()<>]+\)))*\))+(?:\(([^\s()<>'
            . ']+|(\([^\s()<>]+\)))*\)|[^\s\`!()\[\]{};:\'".,<>?«»“”‘’]))#';

        // Standalone hyperlink callback
        $standaloneHyperlinkCallback = function ($match, $scheme = '') {
            $id = $this->generateId();
            $url = $match[1];

            $this->addToken(
                SpanToken::TYPE_LINK,
                $id,
                [
                    'link' => $url,
                    'url' => $scheme . $url,
                ]
            );

            return $id;
        };

        return preg_replace_callback(
            $absoluteUriPattern,
            $standaloneHyperlinkCallback,
            $span
        );
    }

    private function replaceStandaloneEmailAddresses(string $span): string
    {
        // Replace standalone email addresses using a regex based on RFC 5322.
        $emailAddressPattern = '/((?:[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9'
            . '!#$%&\'*+\/=?^_`{|}~-]+)*|"(?:[\x01-\x08\x0b\x0c\x0e-\x1f\x21\x'
            . '23-\x5b\x5d-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f])*")@(?:(?:[a-z'
            . '0-9](?:[a-z0-9-]*[a-z0-9])?\.)+[a-z0-9](?:[a-z0-9-]*[a-z0-9])?|'
            . '\[(?:(?:25[0-5]|2[0-4][0-9]|[01]?[0-9][0-9]?)\.){3}(?:25[0-5]|2'
            . '[0-4][0-9]|[01]?[0-9][0-9]?|[a-z0-9-]*[a-z0-9]:(?:[\x01-\x08\x0'
            . 'b\x0c\x0e-\x1f\x21-\x5a\x53-\x7f]|\\[\x01-\x09\x0b\x0c\x0e-\x7f'
            . '])+)\]))/msi';

        $standaloneEmailAddressCallback = function (array $match) {
            $id = $this->generateId();
            $url = $match[1];

            $this->addToken(
                SpanToken::TYPE_LINK,
                $id,
                [
                    'link' => $url,
                    'url' => 'mailto:' . $url,
                ]
            );

            return $id;
        };

        return preg_replace_callback(
            $emailAddressPattern,
            $standaloneEmailAddressCallback,
            $span
        );
    }

    private function generateId(): string
    {
        $this->tokenId++;

        return sha1($this->prefix . '|' . $this->tokenId);
    }

    private function parseTokens(): string
    {
        $result = '';
        while ($this->lexer->token !== null) {
            switch ($this->lexer->token['type']) {
                case SpanLexer::NAMED_REFERENCE:
                    $result .= $this->createNamedReference(trim($this->lexer->token['value'], '_'));
                    break;
                case SpanLexer::ANONYMOUSE_REFERENCE:
                    $result .= $this->createAnonymousReference(trim($this->lexer->token['value'], '_'));
                    break;
                case SpanLexer::INTERNAL_REFERENCE_START:
                    $result .= $this->parseInternalReference();
                    break;
                case SpanLexer::BACKTICK:
                    $link = $this->parseNamedReference();
                    $result .= $link;
                    break;

                case SpanLexer::NAMED_REFERENCE_END:
                    $result .= $this->createNamedReference($result);
                    break;
                default:
                    $result .= $this->lexer->token['value'];
                    break;
            }

            $this->lexer->moveNext();
        }

        return $result;
    }

    private function parseInternalReference(): string
    {
        $text = '';
        while ($this->lexer->moveNext()) {
            $token = $this->lexer->token;
            switch ($token['type']) {
                case SpanLexer::BACKTICK:
                    return $this->createNamedReference($text);

                default:
                    $text .= $token['value'];
            }
        }

        return $text;
    }

    private function parseNamedReference(): string
    {
        $startPosition = $this->lexer->token['position'];
        $text = '';
        $url = null;
        $this->lexer->moveNext();

        while (true) {
            $token = $this->lexer->token;
            switch ($token['type']) {
                case SpanLexer::NAMED_REFERENCE_END:
                    return $this->createNamedReference($text, $url);

                case SpanLexer::EMBEDED_URL_START:
                    $url = $this->parseEmbeddedUrl();
                    if ($url === null) {
                        $text .= '<';
                    }

                    break;
                default:
                    $text .= $token['value'];
                    break;
            }

            if ($this->lexer->moveNext() === false && $this->lexer->token === null) {
                break;
            }
        }

        $this->lexer->resetPosition($startPosition);
        $this->lexer->moveNext();
        $this->lexer->moveNext();

        return '`';
    }

    private function parseEmbeddedUrl(): ?string
    {
        $startPosition = $this->lexer->token['position'];
        $text = '';
        $this->lexer->moveNext();

        while (true) {
            $token = $this->lexer->token;
            switch ($token['type']) {
                case SpanLexer::NAMED_REFERENCE_END:
                    //We did not find the expected SpanLexer::EMBEDED_URL_END
                    $this->rollback($startPosition);

                    return null;

                case SpanLexer::EMBEDED_URL_END:
                    return $text;

                default:
                    $text .= $token['value'];
            }

            if ($this->lexer->moveNext() === false && $this->lexer->token === null) {
                break;
            }
        }

        $this->rollback($startPosition);

        return null;
    }

    private function rollback(int $position): void
    {
        $this->lexer->resetPosition($position);
        $this->lexer->moveNext();
        $this->lexer->moveNext();
    }
}
