<?php

declare(strict_types=1);

namespace Marios;

/**
 * This is an enum example
 *
 * Enums are introduced in php 8.1.
 */
enum Delivery: string
{
    /**
     * Pickup case
     *
     * Cases can have docblocks.
     */
    case PICKUP = 'pickup';
    case DELIVER = 'deliver';

    public function isDeliver(): bool
    {
        return false;
    }
}
