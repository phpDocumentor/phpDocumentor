<?php

declare(strict_types=1);

namespace Cilex;

if (!class_exists(\Cilex\Application::class, false)) {
    class Application extends \Pimple\Container
    {
    }
}
