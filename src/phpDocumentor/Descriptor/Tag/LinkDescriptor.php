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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the link tag with a descriptor.
 */
class LinkDescriptor extends TagDescriptor
{
    /** @var string $link the url where the link points to. */
    protected $link;

    /**
     * Sets the URL where the link points to.
     *
     * @param string $link
     *
     * @return void
     */
    public function setLink($link)
    {
        $this->link = $link;
    }

    /**
     * Returns the URL where this link points to.
     *
     * @return string
     */
    public function getLink()
    {
        return $this->link;
    }
}
