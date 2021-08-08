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

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

use function array_column;

/**
 * Custom Compiler pass to help symfony to construct the ProjectFactoryStrategies
 *
 * All strategies defined in {@see \phpDocumentor\Reflection\Php\Factory} are injected automatically by our service
 * configuration with a default priority. In some situations this needs to be overwritten. This compiler pass helps us
 * with that part. It will find the tagged services and add them to the {@see ProjectFactoryStrategies}, if multiple
 * `phpdoc.reflection.strategy` tags are defined it will inject them multiple times with different priority.
 * If no priority is defined the default will be used.
 */
final class ReflectionProjectFactoryStrategyPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $strategies = $container->getDefinition(ProjectFactoryStrategies::class);
        foreach ($container->findTaggedServiceIds('phpdoc.reflection.strategy') as $id => $tags) {
            $priotities = array_column($tags, 'priority');
            if (empty($priotities)) {
                $strategies->addMethodCall(
                    'addStrategy',
                    [
                        new Reference($id),
                        ProjectFactoryStrategies::DEFAULT_PRIORITY,
                    ]
                );

                continue;
            }

            foreach ($priotities as $priotity) {
                $strategies->addMethodCall(
                    'addStrategy',
                    [
                        new Reference($id),
                        $priotity ?? ProjectFactoryStrategies::DEFAULT_PRIORITY,
                    ]
                );
            }
        }
    }
}
