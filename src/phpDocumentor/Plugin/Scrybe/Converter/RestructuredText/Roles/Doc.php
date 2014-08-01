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
        $caption = '';

        foreach ($this->node->nodes as $node) {
            $content .= $node->token->content;
        }

        $matches = array();
        if (preg_match('/([^<]*)<?([^>]*)>?/', $content, $matches)) {
            // if the role uses the `caption<content>` notation; extract the two parts
            if (isset($matches[2]) && $matches[2]) {
                $content = $matches[2];
                $caption = trim($matches[1]);
            }
        }

        // check the table of contents for a caption.
        if (!$caption && $this->visitor) {
            $toc = $this->visitor->getTableOfContents();
            $caption = isset($toc[$content]) ? $toc[$content]->getName() : '';
        }

        // if no caption is captured; create one.
        if (!$caption) {
            $caption = str_replace(
                array('-', '_'),
                ' ',
                ucfirst(ltrim(substr(htmlspecialchars($content), strrpos($content, '/')), '\\/'))
            );
        }

        $link = $document->createElement('a', $caption);
        $root->appendChild($link);
        $link->setAttribute('href', str_replace('\\', '/', $content) . '.html');
    }
}
