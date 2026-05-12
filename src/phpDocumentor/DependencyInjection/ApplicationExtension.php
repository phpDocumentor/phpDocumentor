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

use phpDocumentor\Configuration\Definition\Version2;
use phpDocumentor\Configuration\Definition\Version3;
use phpDocumentor\Configuration\SymfonyConfigFactory;
use phpDocumentor\Guides\Nodes\PHP\ClassDiagram;
use phpDocumentor\Guides\Nodes\PHP\ClassList;
use phpDocumentor\Guides\Nodes\PHP\ElementDescription;
use phpDocumentor\Guides\Nodes\PHP\ElementName;
use phpDocumentor\Pipeline\Attribute\Stage;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

use function dirname;
use function file_exists;
use function getcwd;
use function phpDocumentor\Guides\DependencyInjection\template;

final class ApplicationExtension extends Extension implements PrependExtensionInterface
{
    /**
     * The six candidate filenames that phpDocumentor looks for by default, in order of preference.
     */
    private const DEFAULT_CONFIG_FILES = [
        'phpdoc.xml',
        'phpdoc.dist.xml',
        'phpdoc.xml.dist',
        '.phpdoc.xml.dist',
        '.phpdoc.xml',
        '.phpdoc.dist.xml',
    ];

    /**
     * @param string|null $configFile
     *   Absolute path to the configuration file passed via --config/-c on the command line.
     *   When null the extension searches the default file candidates in the current working directory.
     */
    public function __construct(private readonly string|null $configFile = null)
    {
    }

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader(
            $container,
            new FileLocator(dirname(__DIR__, 3) . '/config'),
        );

        $loader->load('services.yaml');

        $container->registerAttributeForAutoconfiguration(
            Stage::class,
            static function (ChildDefinition $definition, Stage $attribute): void {
                $definition->addTag($attribute->name, ['priority' => $attribute->priority]);
            },
        );

        // Merge and validate all configs contributed via prepend() (including the phpdoc.xml
        // config prepended by this extension itself) against the phpdoc.xml v3 schema.
        // The result is stored as a container parameter so that compiler passes and other
        // extensions can read the full configuration before the container is compiled.
        // Note: value-object normalisation (Dsn/Path) is intentionally skipped here; that
        // step is still handled by the service-layer ConfigurationFactory.
        $processedConfig = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter('phpdocumentor.config', $processedConfig);
    }

    public function prepend(ContainerBuilder $container): void
    {
        // Prepend Guides template mappings for PHP-specific node types.
        $container->prependExtensionConfig(
            'guides',
            [
                'templates' => [
                    template(ClassList::class, 'body/php/class-list.html.twig'),
                    template(ClassDiagram::class, 'body/uml.html.twig'),
                    template(ElementName::class, 'body/php/element-name.html.twig'),
                    template(ElementDescription::class, 'body/php/element-description.html.twig'),
                ],
            ],
        );

        // Parse the phpdoc.xml configuration file and make it available as extension
        // config so that load() can merge it with any config contributed by other
        // extensions and validate the result against the full schema.
        $configFile = $this->resolveConfigFile();
        $factory = $this->createSymfonyConfigFactory();

        $config = $configFile !== null
            ? $factory->createFromFile($configFile)
            : $factory->createDefault();

        $container->prependExtensionConfig('phpdocumentor', $config['phpdocumentor']);

        if (! $this->hasMarkdownGuides($config['phpdocumentor'])) {
            return;
        }

        $container->prependExtensionConfig('guides', ['automatic_menu' => true]);
    }

    /**
     * Returns true when at least one guide entry in any version uses the Markdown format.
     *
     * @param array<mixed> $phpdocConfig The parsed phpdocumentor config array (without the outer 'phpdocumentor' key).
     */
    private function hasMarkdownGuides(array $phpdocConfig): bool
    {
        foreach ($phpdocConfig['versions'] ?? [] as $version) {
            foreach ($version['guides'] ?? [] as $guide) {
                if (($guide['format'] ?? '') === 'md') {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Returns the absolute path to the configuration file to use.
     *
     * Uses the explicit --config path when provided, otherwise searches the
     * default candidate filenames in the current working directory.
     */
    private function resolveConfigFile(): string|null
    {
        if ($this->configFile !== null) {
            return $this->configFile;
        }

        $cwd = getcwd();
        foreach (self::DEFAULT_CONFIG_FILES as $candidate) {
            $path = $cwd . '/' . $candidate;
            if (file_exists($path)) {
                return $path;
            }
        }

        return null;
    }

    /**
     * Builds a SymfonyConfigFactory instance that can be used before the DI
     * container is available.  Version2 and Version3 are plain PHP objects and
     * can be instantiated directly.  The template name is set to the same
     * constant default used by {@see Configuration}; the actual runtime default
     * is resolved later by the service-layer ConfigurationFactory.
     */
    private function createSymfonyConfigFactory(): SymfonyConfigFactory
    {
        return new SymfonyConfigFactory([
            '2' => new Version2(Configuration::DEFAULT_TEMPLATE_NAME),
            '3' => new Version3(Configuration::DEFAULT_TEMPLATE_NAME),
        ]);
    }
}
