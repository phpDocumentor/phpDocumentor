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

namespace phpDocumentor\Plugin\Scrybe\Converter\RestructuredText\Visitors;

use \phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;
use ezcDocumentRstDocumentNode;
use ezcDocumentRstSectionNode;

/**
 * A specialized RestructuredText Parser/Visitor to aid in the discovery phase.
 *
 * This class collects all headings and their titles and populates the TableOfContents collection.
 */
class Discover extends Creator
{
    /**
     * This array is meant as a cache of the last entry per depth.
     *
     * To build a hierarchy from a non-recursive method, such as visitSection(), you need a way to reference the last
     * Entry per depth.
     *
     * By keeping track of these pointers you know onto which parent you will need to add a node by checking which of
     * higher depth was parsed last.
     *
     * Important: because it is possible that levels are 'skipped' we will need to unset all 'deeper' depths when
     * setting a new one. Otherwise we might inadvertently add an entry to the wrong tree.
     *
     * @var TableOfContents\Heading[]
     */
    protected $entry_pointers = [];

    /**
     * This is a pointer to the last discovered heading.
     *
     * Directives and roles may 'include' Files as children of the currently parsed heading. Elements as the toctree
     * directive or a plain include are examples of such.
     *
     * @var TableOfContents\Heading
     */
    protected $last_heading = null;

    public function visit(\ezcDocumentRstDocumentNode $ast)
    {
        $toc = $this->getTableOfContents();
        $file = $toc[$this->getFilenameWithoutExtension()];
        $this->entry_pointers[0] = null; // there is no level 0, 1-based list
        $this->entry_pointers[1] = $file;
        $this->last_heading = $file;

        return parent::visit($ast);
    }

    /**
     * Visitor for the section heading used to populate the TableOfContents.
     *
     * This method interprets the heading and its containing text and adds new entries to the TableOfContents object
     * in the RestructuredText document.
     *
     * @see getDocument() for the document containing the TableOfContents.
     * @see phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents for the Table of Contents class.
     */
    protected function visitSection(\DOMNode $root, \ezcDocumentRstNode $node)
    {
        if ($node instanceof ezcDocumentRstSectionNode || $node instanceof ezcDocumentRstDocumentNode) {
            if ($node->depth === 1) {
                $toc = $this->getTableOfContents();
                $file = $toc[$this->getFilenameWithoutExtension()];
                $file->setName($this->nodeToString($node->title));
            } else {
                // find nearest parent pointer depth-wise
                $parent_depth = $node->depth - 1;
                while (!isset($this->entry_pointers[$parent_depth]) && $parent_depth > 0) {
                    --$parent_depth;
                }

                $parent = $this->entry_pointers[$parent_depth];
                $heading = new TableOfContents\Heading($parent);
                $heading->setName($this->nodeToString($node->title));
                $heading->setSlug($node->reference);
                $parent->addChild($heading);

                // set as last indexed heading
                $this->last_heading = $heading;

                // add as new entry pointer
                array_splice($this->entry_pointers, $parent_depth + 1, count($this->entry_pointers), [$heading]);
            }
        }

        parent::visitSection($root, $node);
    }

    /**
     * Adds a TableOfContents File object to the last heading that was discovered.
     *
     * This may be used by roles or directives to insert an include file into the TableOfContents and thus all
     * its headings.
     *
     * This method is explicitly bound to File objects and not other BaseEntry descendents because inline elements
     * such as headings should also modify the internal pointers for this visitor.
     */
    public function addFileToLastHeading(TableOfContents\File $file)
    {
        $this->last_heading->addChild($file);
        $file->setParent($this->last_heading);
    }
}
