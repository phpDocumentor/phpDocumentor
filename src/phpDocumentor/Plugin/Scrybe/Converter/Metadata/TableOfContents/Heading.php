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

namespace phpDocumentor\Plugin\Scrybe\Converter\Metadata\TableOfContents;

/**
 * The Table of Contents File describes a headings and the Files and subentries it may contain.
 *
 * A Heading may also contain files, those will serve as containers for more headings or other files. This way it is
 * possible to 'include' another File as part of a hierarchy and have a integrated table of contents.
 */
class Heading extends BaseEntry
{
    /** @var string the slug used by the anchor */
    protected $slug;

    /**
     * Sets the anchor slug for this entry.
     *
     * @param string $slug
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;
    }

    /**
     * Retrieves the anchor slug for this entry.
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    public function getFilename()
    {
        /** @var Heading|File $parent */
        $parent = $this->getParent();
        return $parent ? $parent->getFilename() : null;
    }
}
