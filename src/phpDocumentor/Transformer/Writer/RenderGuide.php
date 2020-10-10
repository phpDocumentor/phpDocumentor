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
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\LoadCacheCommand;
use phpDocumentor\Guides\PersistCacheCommand;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Guides\RestructuredText\ParseDirectoryCommand;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\Parser\FlySystemFactory;
use phpDocumentor\Transformer\Transformation;
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

    /** @var CommandBus */
    private $commandBus;

    /** @var Renderer */
    private $renderer;

    public function __construct(
        FlySystemFactory $flySystemFactory,
        Renderer $renderer,
        Locator $cacheLocator,
        LoggerInterface $logger,
        CommandBus $commandBus
    ) {
        $this->flySystemFactory = $flySystemFactory;
        $this->cacheLocator = $cacheLocator;
        $this->logger = $logger;
        $this->commandBus = $commandBus;
        $this->renderer = $renderer;
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
                $this->renderDocumentationSet($documentationSet, $project, $transformation, $cachePath);
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
        Transformation $transformation,
        string $cachePath
    ) : void {
        $dsn = $documentationSet->getSource()['dsn'];
        $stopwatch = $this->startRenderingSetMessage($dsn);
        $useCache = $project->getSettings()->getCustom()[self::SETTING_CACHE];

//        $this->commandBus->handle(new LoadCacheCommand($cachePath, $useCache));

        $this->renderer->initialize($project, $documentationSet, $transformation);
        $this->render($transformation->getTransformer()->destination(), $documentationSet->getOutput());

//        $this->commandBus->handle(new PersistCacheCommand($cachePath, $useCache));

        $this->completedRenderingSetMessage($stopwatch, $dsn);
    }

    private function startRenderingSetMessage(Dsn $dsn) : Stopwatch
    {
        $stopwatch = new Stopwatch();
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

    private function render(FilesystemInterface $destination, string $targetDirectory) : void
    {
        $this->commandBus->handle(new RenderCommand($destination, $targetDirectory));
    }
}
