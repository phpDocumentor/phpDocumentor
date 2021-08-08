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

namespace phpDocumentor\Descriptor;

/**
 * Base class for descriptors containing the most used options.
 */
interface Descriptor
{
    /**
     * Returns the local name for this element.
     */
    public function getName(): string;

    /**
     * Returns the description for this element.
     *
     * This method will automatically attempt to inherit the parent's description if this one has none.
     */
    public function getDescription(): ?DocBlock\DescriptionDescriptor;
}
