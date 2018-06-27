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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Roles;

use ezcDocumentRstNode;
use phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors\Creator;

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
     */
    public function toDocbook(\DOMDocument $document, \DOMElement $root)
    {
    }

    /**
     * Transform text role to HTML.
     *
     * Create a XHTML structure at the text roles position in the document.
     */
    public function toXhtml(\DOMDocument $document, \DOMElement $root)
    {
        $content = '';
        $caption = '';

        /** @var ezcDocumentRstNode $node */
        $node = $this->node;

        foreach ($node->nodes as $node) {
            $content .= $node->token->content;
        }

        $matches = [];
        if (preg_match('/([^<]*)<?([^>]*)>?/', $content, $matches)) {
            // if the role uses the `caption<content>` notation; extract the two parts
            if (isset($matches[2]) && $matches[2]) {
                $content = $matches[2];
                $caption = trim($matches[1]);
            }
        }

        // check the table of contents for a caption.
        if (!$caption && $this->visitor && $this->visitor instanceof Creator) {
            $toc = $this->visitor->getTableOfContents();
            $caption = isset($toc[$content]) ? $toc[$content]->getName() : '';
        }

        // if no caption is captured; create one.
        if (!$caption) {
            $caption = str_replace(
                ['-', '_'],
                ' ',
                ucfirst(ltrim(substr(htmlspecialchars($content), strrpos($content, '/')), '\\/'))
            );
        }

        $link = $document->createElement('a', $caption);
        $root->appendChild($link);
        $link->setAttribute('href', str_replace('\\', '/', $content) . '.html');
    }
}
