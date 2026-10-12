<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link https://phpdoc.org
 */

use phpDocumentor\GuidesDirectives\Directives\DirectiveList;
use phpDocumentor\GuidesDirectives\Directives\DirectiveOptionsList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->defaults()
        ->autowire()
        ->autoconfigure()
        ->set(DirectiveOptionsList::class)
        ->tag('phpdoc.guides.directive')
        ->set(DirectiveList::class)
        ->tag('phpdoc.guides.directive');
};
