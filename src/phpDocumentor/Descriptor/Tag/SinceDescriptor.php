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
 * Descriptor representing the since tag with another descriptor.
 *
 * @api
 * @package phpDocumentor\AST\Tags
 */
class SinceDescriptor extends TagDescriptor
{
    /** @var string $version represents the version since when the associated element was introduced */
    protected $version = '';

    /**
     * Returns the version when the associated element was introduced.
     */
    public function getVersion(): string
    {
        return $this->version;
    }

    /**
     * Sets the version since when the associated element was introduced.
     */
    public function setVersion(string $version): void
    {
        $this->version = $version;
    }
}
