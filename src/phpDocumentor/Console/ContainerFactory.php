<?php

declare(strict_types=1);

namespace phpDocumentor\Console;

use phpDocumentor\DependencyInjection\ApplicationExtension;
use phpDocumentor\DependencyInjection\GuidesCommandsPass;
use phpDocumentor\DependencyInjection\ReflectionProjectFactoryStrategyPass;
use phpDocumentor\Extension\ExtensionHandler;
use phpDocumentor\Guides\DependencyInjection\GuidesExtension;
use phpDocumentor\Guides\Graphs\DependencyInjection\GraphsExtension;
use phpDocumentor\Guides\Markdown\DependencyInjection\MarkdownExtension;
use phpDocumentor\Guides\RestructuredText\DependencyInjection\ReStructuredTextExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\ExtensionInterface;

use function array_merge;
use function dirname;

final class ContainerFactory
{
    private readonly ContainerBuilder $container;

    /** @param list<ExtensionInterface> $defaultExtensions */
    public function __construct(array $defaultExtensions = [])
    {
        $this->container = new ContainerBuilder();
        $this->container->addCompilerPass(new GuidesCommandsPass());
        $this->container->addCompilerPass(new ReflectionProjectFactoryStrategyPass());

        foreach (
            array_merge(
                [
                    new ApplicationExtension(),
                    new GuidesExtension(),
                    new ReStructuredTextExtension(),
                    new MarkdownExtension(),
                    new GraphsExtension(),
                ],
                $defaultExtensions,
            ) as $extension
        ) {
            $this->registerExtension($extension);
        }
    }

    private function registerExtension(ExtensionInterface $extension): void
    {
        $this->container->registerExtension($extension);
        $this->container->loadFromExtension($extension->getAlias());
    }

    public function create(
        string $vendorDir,
        ExtensionHandler $extensionHandler,
    ): ContainerBuilder {
        $this->container->setParameter('vendor_dir', $vendorDir);
        $this->container->setParameter('kernel.project_dir', dirname(__DIR__, 3));
        $this->container->setParameter('env(PHPDOC_PLANTUML)', 'plantuml_smetana');
        $this->container->setParameter('guides.graphs.renderer', '%env(PHPDOC_PLANTUML)%');
        $this->container->setParameter('env(PHPDOC_PLANTUML_BIN)', '%kernel.project_dir%/bin/plantuml');
        $this->container->setParameter('env(PHPDOC_PLANTUML_SERVER)', 'https://www.plantuml.com/plantuml/svg');
        $this->container->setParameter('guides.graphs.plantuml_binary', '%env(PHPDOC_PLANTUML_BIN)%');
        $this->container->setParameter('guides.graphs.plantuml_server', '%env(PHPDOC_PLANTUML_SERVER)%');

        foreach ($extensionHandler->loadExtensions() as $extension) {
            $this->registerExtension(new $extension());
        }

        $this->container->compile(true);

        return $this->container;
    }
}
