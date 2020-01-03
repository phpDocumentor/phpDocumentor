<?php

declare(strict_types=1);

namespace Marios;

/**
 * Entrypoint for this pizza ordering application.
 *
 * This class provides an interface through which you can order pizza's and pasta's from Mario's Pizzeria.
 */
final class Pizzeria implements \JsonSerializable
{
    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return [];
    }
}
