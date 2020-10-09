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

namespace phpDocumentor\Transformer\Writer;

use League\Flysystem\Filesystem;
use League\Flysystem\FilesystemInterface;
use League\Tactician\CommandBus;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\RestructuredText\Command\LoadCacheCommand;
use phpDocumentor\Guides\RestructuredText\Command\ParseDirectoryCommand;
use phpDocumentor\Guides\RestructuredText\Command\PersistCacheCommand;
use phpDocumentor\Guides\RestructuredText\Command\RenderCommand;
use phpDocumentor\Guides\RestructuredText\HTML\HTMLFormat;
use phpDocumentor\Guides\RestructuredText\LaTeX\LaTeXFormat;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use function sprintf;

/**
 * @experimental Do not use; this stage is meant as a sandbox / playground to experiment with generating guides.
 */
final class RenderGuide extends WriterAbstract implements ProjectDescriptor\WithCustomSettings
{
    public const FEATURE_FLAG = 'guides.enabled';
    private const SETTING_CACHE = 'guides.cache';

    /** @var FlySystemFactory */
    private $flySystemFactory;

    /** @var Locator */
    private $cacheLocator;

    /** @var LoggerInterface */
    private $logger;

    /** @var EnvironmentFactory */
    private $environmentFactory;

    /** @var CommandBus */
    private $commandBus;

    /** @var string */
    private $globalTemplatesPath;

    public function __construct(
        FlySystemFactory $flySystemFactory,
        Locator $cacheLocator,
        LoggerInterface $logger,
        EnvironmentFactory $environmentFactory,
        CommandBus $commandBus,
        string $globalTemplatesPath
    ) {
        $this->flySystemFactory = $flySystemFactory;
        $this->cacheLocator = $cacheLocator;
        $this->logger = $logger;
        $this->environmentFactory = $environmentFactory;
        $this->commandBus = $commandBus;
        $this->globalTemplatesPath = $globalTemplatesPath;
    }

    public function transform(ProjectDescriptor $project, Transformation $transformation) : void
    {
        // Feature flag: Guides are disabled by default since this is an experimental feature
        if (!($project->getSettings()->getCustom()[self::FEATURE_FLAG])) {
            return;
        }

        $this->logger->warning(
            'Generating guides is experimental, no BC guarantees are given, use at your own risk'
        );

        $cachePath = (string) $this->cacheLocator->locate('guide');

        /** @var VersionDescriptor $version */
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                $this->renderDocumentationSet($documentationSet, $project, $cachePath, $transformation);
            }
        }
    }

    public function getDefaultSettings() : array
    {
        return [
            self::FEATURE_FLAG => false,
            self::SETTING_CACHE => true,
        ];
    }

    private function renderDocumentationSet(
        DocumentationSetDescriptor $documentationSet,
        ProjectDescriptor $project,
        string $cachePath,
        Transformation $transformation
    ) : void {
        $destination = $transformation->getTransformer()->destination();
        $dsn = $documentationSet->getSource()['dsn'];
        $stopwatch = $this->startRenderingSetMessage($dsn);
        $useCache = $project->getSettings()->getCustom()[self::SETTING_CACHE];

        $origin = $this->flySystemFactory->create($dsn);
        $directory = $documentationSet->getSource()['paths'][0] ?? '';
        $targetDirectory = $documentationSet->getOutput();

        $this->commandBus->handle(new LoadCacheCommand($cachePath, $useCache));
        $this->parse(
            $targetDirectory,
            $cachePath,
            $useCache,
            $project,
            $transformation,
            $origin,
            $directory
        );
        $this->render($destination, $targetDirectory);

        $this->commandBus->handle(new PersistCacheCommand($cachePath, $useCache));

        $this->completedRenderingSetMessage($stopwatch, $dsn);
    }

    private function startRenderingSetMessage(Dsn $dsn) : Stopwatch
    {
        $stopwatch = new Stopwatch(true);
        $stopwatch->start('guide');
        $this->logger->info('Rendering guide ' . $dsn);

        return $stopwatch;
    }

    private function completedRenderingSetMessage(Stopwatch $stopwatch, Dsn $dsn) : void
    {
        $stopwatchEvent = $stopwatch->stop('guide');
        $this->logger->info(
            sprintf(
                'Completed rendering guide %s in %.2fms using %.2f mb memory',
                (string) $dsn,
                $stopwatchEvent->getDuration(),
                $stopwatchEvent->getMemory() / 1024 / 1024
            )
        );
    }

    private function parse(
        string $targetDirectory,
        string $cachePath,
        bool $useCache,
        ProjectDescriptor $project,
        Transformation $transformation,
        Filesystem $origin,
        string $directory
    ) : void {
        $environment = $this->environmentFactory->create($project, $transformation, $targetDirectory);

        $templateRenderer = new TemplateRenderer($environment, 'guides');
        $environment->addExtension(new AssetsExtension());

        $configuration = new Configuration();
        $configuration->setTemplateRenderer($templateRenderer);
        $configuration->setCacheDir($cachePath);
        $configuration->setUseCachedMetas($useCache);

        $configuration->addFormat(new HTMLFormat($templateRenderer, $this->globalTemplatesPath, $targetDirectory));
        $configuration->addFormat(new LaTeXFormat($templateRenderer));

        $this->commandBus->handle(new ParseDirectoryCommand($configuration, $origin, $directory));
    }

    private function render(FilesystemInterface $destination, string $targetDirectory) : void
    {
        $this->commandBus->handle(new RenderCommand($destination, $targetDirectory));
    }
}
