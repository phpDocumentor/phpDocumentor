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

namespace phpDocumentor\Guides\Renderers\Html;

use Highlight\Highlighter;
use phpDocumentor\Guides\Nodes\CodeNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\Renderers\NodeRenderer;
use function array_keys;
use function array_merge;
use function array_reverse;
use function count;
use function implode;
use function in_array;
use function preg_split;
use function rtrim;
use function str_pad;
use function trim;
use const STR_PAD_LEFT;

class CodeNodeRenderer implements NodeRenderer
{
//    private static $isHighlighterConfigured = false;

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

    /** @var Renderer */
    private $renderer;

    public function __construct(CodeNode $codeNode)
    {
        $this->codeNode = $codeNode;
        $this->renderer = $codeNode->getEnvironment()->getRenderer();
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

        if ($language !== 'text' && $language !== '') {
            $highLighter = new Highlighter();
            $code = $highLighter->highlight(self::LANGUAGES_MAPPING[$language] ?? $language, $code)->value;
        }

        return $this->renderer->render(
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

    /**
     * @return array<string>
     */
    private function getLines(string $code) : array
    {
        $lines = preg_split('/\r\n|\r|\n/', $code);
        $reversedLines = array_reverse($lines);

        // trim empty lines at the end of the code
        foreach ($reversedLines as $key => $line) {
            if (trim($line) !== '') {
                break;
            }

            unset($reversedLines[$key]);
        }

        return array_reverse($reversedLines);
    }

    private function configureHighlighter() : void
    {
//        if (!self::$isHighlighterConfigured) {
//            Highlighter::registerLanguage('php', 'guides/highlight.php/php.json', true);
//            Highlighter::registerLanguage('twig', 'guides/highlight.php/twig.json', true);
//        }
//
//        self::$isHighlighterConfigured = true;
    }
}
