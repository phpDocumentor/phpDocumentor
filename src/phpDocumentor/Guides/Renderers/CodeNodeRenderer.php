<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 * @author Ryan Weaver <ryan@symfonycasts.com> on the original DocBuilder.
 * @author Mike van Riel <me@mikevanriel.com> for adapting this to phpDocumentor.
 */

namespace phpDocumentor\Guides\Renderers;

use Doctrine\RST\Nodes\CodeNode;
use Doctrine\RST\Renderers\NodeRenderer;
use Doctrine\RST\Templates\TemplateRenderer;
use Highlight\Highlighter;
use function count;
use function in_array;

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

    /** @var string */
    private $globalTemplatesPath;

    public function __construct(CodeNode $codeNode, TemplateRenderer $templateRenderer, string $globalTemplatesPath)
    {
        $this->codeNode = $codeNode;
        $this->templateRenderer = $templateRenderer;
        $this->globalTemplatesPath = $globalTemplatesPath;
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
        for ($i = 1, $nbLines = count($lines); $i <= $nbLines; ++$i) {
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
                'codeNode' => $this->codeNode,
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

        return in_array($lang, $supportedLanguages, true);
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

    private function configureHighlighter() : void
    {
        if (!self::$isHighlighterConfigured) {
            Highlighter::registerLanguage('php', $this->globalTemplatesPath . '/guides/highlight.php/php.json', true);
            Highlighter::registerLanguage('twig', $this->globalTemplatesPath . '/guides/highlight.php/twig.json', true);
        }

        self::$isHighlighterConfigured = true;
    }
}
