<?php
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2013 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Roles;

/**
 * The :doc: role creates a link to an external document.
 *
 * For this link you can either use relative locations or an absolute notation.
 * The absolute notation uses the documentation root as starting directory.
 */
class Doc extends \ezcDocumentRstTextRole implements \ezcDocumentRstXhtmlTextRole
{
    /**
     * Transform text role to docbook.
     *
     * Create a docbook XML structure at the text roles position in the document.
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
     * Transform text role to HTML.
     *
     * Create a XHTML structure at the text roles position in the document.
     *
     * @param \DOMDocument $document
     * @param \DOMElement  $root
     *
     * @return void
     */
    public function toXhtml(\DOMDocument $document, \DOMElement $root)
    {
        $content = '';
        foreach ($this->node->nodes as $node) {
            $content .= $node->token->content;
        }

        $link = $document->createElement(
            'a',
            str_replace(
                array('-', '_'),
                ' ',
                ucfirst(ltrim(substr(htmlspecialchars($content), strrpos($content, '/')), '\\/'))
            )
        );
        $root->appendChild($link);
        $link->setAttribute('href', str_replace('\\', '/', $content) . '.html');
    }
}
