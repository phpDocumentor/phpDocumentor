<?php

declare(strict_types=1);

namespace Pimple;

if (!class_exists(\Pimple\ServiceProviderInterface::class, false)) {
    interface ServiceProviderInterface
    {
        public function register(\Pimple\Container $app);
    }
}
