<?php

declare(strict_types=1);

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
