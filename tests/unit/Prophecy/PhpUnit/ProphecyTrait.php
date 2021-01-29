<?php

declare(strict_types=1);

namespace Prophecy\PhpUnit;

use const PHP_VERSION_ID;

if (PHP_VERSION_ID < 70300) {
    trait ProphecyTrait
    {
    }

}
