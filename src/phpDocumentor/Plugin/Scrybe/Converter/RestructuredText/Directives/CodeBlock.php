<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2014 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives;

/**
 * Directive used to process `.. toctree::` and insert entries from the table of contents.
 *
 * This directive tries to match the file with an entry in the table of contents during the creation phase. If a
 * document is found it will generate a mini-table of contents at that location with the depth given using the
 * `:maxdepth:` parameter.
 *
 * This directive is inspired by {@link http://sphinx.pocoo.org/concepts.html#the-toc-tree Sphinx' toctree} directive.
 */
class CodeBlock extends \ezcDocumentRstDirective implements \ezcDocumentRstXhtmlDirective
{
    /**
     * Transform directive to Docbook.
     *
     * Create a docbook XML structure at the directives position in the document.
     *
     * @param \DOMDocument $document
     * @param \DOMElement  $root
     *
     * @return void
     */
    public function toDocbook(\DOMDocument $document, \DOMElement $root)
    {
    }

    /**
     * Transform directive to HTML
     *
     * Create a XHTML structure at the directives position in the document.
     *
     * @param \DOMDocument $document
     * @param \DOMElement  $root
     *
     * @todo use the TableofContents collection to extract a sublisting up to the given depth or 2 if none is provided
     *
     * @return void
     */
    public function toXhtml(\DOMDocument $document, \DOMElement $root)
    {
        $content = '';

        /** @var \ezcDocumentRstToken $token */
        foreach ($this->node->tokens as $token) {
            $content .= $token->content;
        }
        $content = rtrim($content, "\n");

        $pre = $document->createElement('pre');
        $root->appendChild($pre);
        $code = $document->createElement('code', $content);
        $pre->appendChild($code);
    }
}
