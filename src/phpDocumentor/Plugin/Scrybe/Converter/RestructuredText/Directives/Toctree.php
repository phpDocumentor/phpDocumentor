<?php
declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author    Mike van Riel <mike.vanriel@naenius.com>
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Directives;

use phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Creator;
use phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Discover;

/**
 * Directive used to process `.. toctree::` and insert entries from the table of contents.
 *
 * This directive tries to match the file with an entry in the table of contents during the creation phase. If a
 * document is found it will generate a mini-table of contents at that location with the depth given using the
 * `:maxdepth:` parameter.
 *
 * Another option is :hidden: that will hide the toc from view while still making connections.
 *
 * This directive is inspired by {@link http://sphinx.pocoo.org/concepts.html#the-toc-tree Sphinx' toctree} directive.
 */
class Toctree extends \ezcDocumentRstDirective implements \ezcDocumentRstXhtmlDirective
{
    protected $links = [];

    public function __construct(\ezcDocumentRstDocumentNode $ast, $path, \ezcDocumentRstDirectiveNode $node)
    {
        parent::__construct($ast, $path, $node);

        $this->parseLinks();
    }

    protected function parseLinks()
    {
        $line = '';

        /** @var \ezcDocumentRstToken $token */
        foreach ($this->node->tokens as $token) {
            if ($token->type === 2) {
                $line = trim($line);
                if ($line) {
                    $this->links[] = $line;
                    $line = '';
                }
            }

            if ($token->type !== 5 && $token->type !== 4) {
                continue;
            }

            $line .= $token->content;
        }
    }

    /**
     * Transform directive to docbook
     *
     * Create a docbook XML structure at the directives position in the document.
     */
    public function toDocbook(\DOMDocument $document, \DOMElement $root)
    {
    }

    /**
     * Transform directive to HTML
     *
     * Create a XHTML structure at the directives position in the document.
     *
     * @todo use the TableofContents collection to extract a sublisting up to the given depth or 2 if none is provided
     */
    public function toXhtml(\DOMDocument $document, \DOMElement $root)
    {
        $this->addLinksToTableOfContents();

        // if the hidden flag is set then this item should not be rendered but still processed (see above)
        if (isset($this->node->options['hidden'])) {
            return;
        }

        $list = $document->createElement('ol');
        $root->appendChild($list);

        foreach ($this->links as $link) {
            $list_item = $document->createElement('li');
            $list->appendChild($list_item);

            $link_element = $document->createElement('a');
            $list_item->appendChild($link_element);
            $link_element->appendChild($document->createTextNode($this->getCaption($link)));
            $link_element->setAttribute('href', str_replace('\\', '/', $link) . '.html');
        }
    }

    protected function addLinksToTableOfContents()
    {
        foreach ($this->links as $file_name) {
            /** @var Discover $visitor */
            $visitor = $this->visitor;
            if ($visitor instanceof Discover) {
                $toc = $visitor->getTableOfContents();
                $file = $toc[$file_name];

                $visitor->addFileToLastHeading($file);
            }
        }
    }

    /**
     * Retrieves the caption for the given $token.
     *
     * The caption is retrieved by converting the filename to a human-readable format.
     *
     * @param \ezcDocumentRstToken $file_name
     *
     * @return string
     */
    protected function getCaption($file_name)
    {
        if ($this->visitor instanceof Creator) {
            $toc = $this->visitor->getTableOfContents();
            $name = $toc[$file_name]->getName();
            return $name ?: $file_name;
        }

        return $file_name;
    }
}
