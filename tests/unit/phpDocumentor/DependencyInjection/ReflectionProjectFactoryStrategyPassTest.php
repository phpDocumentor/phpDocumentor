<?php

declare(strict_types=1);

namespace phpDocumentor\DependencyInjection;

use phpDocumentor\Reflection\Php\ProjectFactoryStrategies;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

/**
 * @coversDefaultClass \phpDocumentor\DependencyInjection\ReflectionProjectFactoryStrategyPass
 */
final class ReflectionProjectFactoryStrategyPassTest extends TestCase
{
    use ProphecyTrait;

    /** @covers ::process */
    public function testTaggedStrategiesAreInjectedWithDefaultPriority(): void
    {
        $serviceDefinition = new Definition(ProjectFactoryStrategies::class);

        $container = $this->prophesize(ContainerBuilder::class);
        $container->getDefinition(ProjectFactoryStrategies::class)
            ->willReturn($serviceDefinition);

        $container->findTaggedServiceIds('phpdoc.reflection.strategy')->willReturn(
            [
                'myStrategy' => [],
            ]
        );

        $compilerPass = new ReflectionProjectFactoryStrategyPass();

        $compilerPass->process($container->reveal());

        self::assertEquals(
            [
                [
                    'addStrategy',
                    [
                        new Reference('myStrategy'),
                        ProjectFactoryStrategies::DEFAULT_PRIORITY,
                    ],
                ],
            ],
            $serviceDefinition->getMethodCalls()
        );
    }

    /** @covers ::process */
    public function testTaggedStrategiesAreInjectedWithOverwrittenPriority(): void
    {
        $serviceDefinition = new Definition(ProjectFactoryStrategies::class);

        $container = $this->prophesize(ContainerBuilder::class);
        $container->getDefinition(ProjectFactoryStrategies::class)
            ->willReturn($serviceDefinition);

        $container->findTaggedServiceIds('phpdoc.reflection.strategy')->willReturn(
            [
                'myStrategy' => [],
                'myStrategyOther' => [
                    ['priority' => 1100],
                    [],
                ],
            ]
        );

        $compilerPass = new ReflectionProjectFactoryStrategyPass();

        $compilerPass->process($container->reveal());

        self::assertEquals(
            [
                [
                    'addStrategy',
                    [
                        new Reference('myStrategy'),
                        ProjectFactoryStrategies::DEFAULT_PRIORITY,
                    ],
                ],
                [
                    'addStrategy',
                    [
                        new Reference('myStrategyOther'),
                        1100,
                    ],
                ],
            ],
            $serviceDefinition->getMethodCalls()
        );
    }
}
