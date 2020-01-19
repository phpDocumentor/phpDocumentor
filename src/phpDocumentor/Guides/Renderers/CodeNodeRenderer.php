<?php

declare(strict_types=1);

/*
 * This file is part of the Docs Builder package.
 * (c) Ryan Weaver <ryan@symfonycasts.com>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace phpDocumentor\Guides\Renderers;

use Doctrine\RST\Nodes\CodeNode;
use Doctrine\RST\Renderers\NodeRenderer;
use Doctrine\RST\Templates\TemplateRenderer;
use Highlight\Highlighter;

class CodeNodeRenderer implements NodeRenderer
{
    private static $isHighlighterConfigured = false;

    private const LANGUAGES_MAPPING = [
        'html+jinja' => 'twig',
        'html+twig' => 'twig',
        'jinja' => 'twig',
        'html+php' => 'html',
        'xml+php' => 'xml',
        'php-annotations' => 'php',
        'terminal' => 'bash',
        'rst' => 'markdown',
        'php-standalone' => 'php',
        'php-symfony' => 'php',
        'varnish4' => 'c',
        'varnish3' => 'c',
        'vcl' => 'c',
    ];

    /** @var CodeNode */
    private $codeNode;

    /** @var TemplateRenderer */
    private $templateRenderer;

    public function __construct(CodeNode $codeNode, TemplateRenderer $templateRenderer)
    {
        $this->codeNode = $codeNode;
        $this->templateRenderer = $templateRenderer;
    }

    public function render() : string
    {
        $this->configureHighlighter();

        $value = $this->codeNode->getValue();

        if ($this->codeNode->isRaw()) {
            return $value;
        }

        $lines = $this->getLines($value);
        $code = implode("\n", $lines);

        $lineNumbers = '';
        for ($i = 1; $i <= \count($lines); ++$i) {
            $lineNumbers .= str_pad((string) $i, 2, ' ', STR_PAD_LEFT) . "\n";
        }

        $language = $this->codeNode->getLanguage() ?? 'php';

        if ('text' !== $language) {
            $highLighter = new Highlighter();
            $code = $highLighter->highlight(self::LANGUAGES_MAPPING[$language] ?? $language, $code)->value;
        }

        return $this->templateRenderer->render(
            'code.html.twig',
            [
                'language' => $language,
                'languageMapping' => self::LANGUAGES_MAPPING[$language] ?? $language,
                'code' => $code,
                'lineNumbers' => rtrim($lineNumbers),
            ]
        );
    }

    public static function isLanguageSupported(string $lang) : bool
    {
        $highlighter = new Highlighter();
        $supportedLanguages = array_merge(
            array_keys(self::LANGUAGES_MAPPING),
            $highlighter->listLanguages(true),
            // not highlighted, but valid
            ['text']
        );

        return \in_array($lang, $supportedLanguages);
    }

    private function getLines(string $code) : array
    {
        $lines = preg_split('/\r\n|\r|\n/', $code);
        $reversedLines = array_reverse($lines);

        // trim empty lines at the end of the code
        foreach ($reversedLines as $key => $line) {
            if ('' !== trim($line)) {
                break;
            }

            unset($reversedLines[$key]);
        }

        return array_reverse($reversedLines);
    }

    private function configureHighlighter()
    {
        if (false === self::$isHighlighterConfigured) {
            Highlighter::registerLanguage('php', __DIR__ . '/../Templates/highlight.php/php.json', true);
            Highlighter::registerLanguage('twig', __DIR__ . '/../Templates/highlight.php/twig.json', true);
        }

        self::$isHighlighterConfigured = true;
    }
}
