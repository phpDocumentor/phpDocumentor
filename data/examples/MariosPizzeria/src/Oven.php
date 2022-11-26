<?php

declare(strict_types=1);

namespace Marios;

/**
 * @used-by Pizzaria
 */
final class Oven
{
    private const DEFAULT_TEMPERATURE=220;

    /**
     * @param int $temp the temperature in degrees celcius
     */
    public function heatToTemp(int &$temp = self::DEFAULT_TEMPERATURE) : void
    {

    }
}
