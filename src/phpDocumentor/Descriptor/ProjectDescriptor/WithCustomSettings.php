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

namespace phpDocumentor\Descriptor\ProjectDescriptor;

/**
 * Declares that the service implementing this interface yields its own settings.
 *
 * Some services, such as the Graph Writer, have their own custom settings.
 */
interface WithCustomSettings
{
    /**
     * @return array<string, bool>
     */
    public function getDefaultSettings(): array;
}
