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

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the link tag with a descriptor.
 *
 * @api
 * @package phpDocumentor\AST\Tags
 */
class LinkDescriptor extends TagDescriptor
{
    /** @var string $link the url where the link points to. */
    protected $link = '';

    /**
     * Sets the URL where the link points to.
     */
    public function setLink(string $link) : void
    {
        $this->link = $link;
    }

    /**
     * Returns the URL where this link points to.
     */
    public function getLink() : string
    {
        return $this->link;
    }
}
