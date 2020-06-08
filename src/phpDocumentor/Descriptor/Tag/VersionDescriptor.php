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
 * Descriptor representing the version tag on a class, interface, trait or file.
 *
 * @api
 * @package phpDocumentor\AST\Tags
 */
class VersionDescriptor extends TagDescriptor
{
    /** @var string $version Version string representing the current version of the element */
    protected $version = '';

    /**
     * Returns the current version for the associated element.
     */
    public function getVersion() : string
    {
        return $this->version;
    }

    /**
     * Sets the version for the associated element.
     */
    public function setVersion(string $version) : void
    {
        $this->version = $version;
    }
}
