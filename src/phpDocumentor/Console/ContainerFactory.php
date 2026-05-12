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
        string|null $configFile = null,
    ): ContainerBuilder {
        // ApplicationExtension must be registered in create() (not the constructor) so that
        // it can receive the config file path resolved from the command-line --config option.
        // prepend() is called by compile() for all PrependExtensionInterface implementations,
        // but only after all extensions have been registered via loadFromExtension().
        // Registering first ensures prepend order is correct relative to other extensions.
        $this->registerExtension(new ApplicationExtension($configFile));

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
