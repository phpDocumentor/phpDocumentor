<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Descriptor\Tag;

use phpDocumentor\Descriptor\TagDescriptor;

/**
 * Descriptor representing the deprecated tag with a descriptor.
 */
final class DeprecatedDescriptor extends TagDescriptor
{
    /** @var string $version represents the version since when the element was deprecated. */
    protected $version;

    /**
     * Returns the version since when the associated element was deprecated.
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * Sets the version since when the associated element was deprecated.
     */
    public function setVersion(string $version) : void
    {
        $this->version = $version;
    }
}
