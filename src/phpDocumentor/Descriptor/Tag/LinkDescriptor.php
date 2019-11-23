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
