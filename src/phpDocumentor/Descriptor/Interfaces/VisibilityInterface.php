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

namespace phpDocumentor\Descriptor\Interfaces;

interface VisibilityInterface
{
    /**
     * Returns the visibility for this element.
     *
     * The following values are supported:
     *
     * - public
     * - protected
     * - private
     */
    public function getVisibility() : string;
}
