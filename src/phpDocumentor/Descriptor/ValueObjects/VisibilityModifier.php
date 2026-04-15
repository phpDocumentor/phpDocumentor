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

namespace phpDocumentor\Descriptor\ValueObjects;

enum VisibilityModifier: string
{
    case PUBLIC = 'public';
    case PROTECTED = 'protected';
    case PRIVATE = 'private';

    public function getWeight(): int
    {
        return match ($this) {
            self::PUBLIC => 0,
            self::PROTECTED => 1,
            self::PRIVATE => 2,
        };
    }
}
