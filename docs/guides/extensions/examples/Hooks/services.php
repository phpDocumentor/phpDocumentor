<?php

declare(strict_types=1);

use phpDocumentor\WordpressHooks\Reflection\Php\Factory\ActionFactory;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->set(ActionFactory::class)
        ->tag('phpdoc.reflection.strategy');
};
