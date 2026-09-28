<?php

declare(strict_types=1);

use phpDocumentor\Example\Directives\HelloDirective;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->set(HelloDirective::class)
        ->tag('phpdoc.guides.directive')
    ;
};
