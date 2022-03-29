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

function turnOffOven(int $timeout = 0): void
{
}

/**
 * @deprecated
 */
function populateTemperature(int &$temperature): void
{
}
