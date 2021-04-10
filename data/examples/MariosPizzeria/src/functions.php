<?php

namespace Marios;

function heatOven()
{
}

/**
 * @return bool whether cooling succeeded.
 */
function coolOven(int $degrees = 42): bool
{
    return true;
}

/**
 * @deprecated
 */
function populateTemperature(int &$temperature): void
{
}
