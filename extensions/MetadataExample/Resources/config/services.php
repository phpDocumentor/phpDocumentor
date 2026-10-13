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

use phpDocumentor\MetadataExample\Reflection\ExampleReducer;
use phpDocumentor\MetadataExample\Reflection\FileMetadataMiddleware;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $container): void {
    $container->services()
        ->set(ExampleReducer::class)
        ->tag('phpdoc.reflection.reducer')
        ->set(FileMetadataMiddleware::class)
        ->tag('phpdoc.parser.middleware');
};
