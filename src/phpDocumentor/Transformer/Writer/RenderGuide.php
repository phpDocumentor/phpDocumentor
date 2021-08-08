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

use League\Tactician\CommandBus;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\Guides\Configuration;
use phpDocumentor\Guides\Formats\Format;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Renderer;
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

    /** @var LoggerInterface */
    private $logger;

    /** @var CommandBus */
    private $commandBus;

    /** @var Renderer */
    private $renderer;

    /** @var iterable<Format> */
    private $outputFormats;

    /** @param iterable<Format> $outputFormats */
    public function __construct(
        Renderer $renderer,
        LoggerInterface $logger,
        CommandBus $commandBus,
        iterable $outputFormats
    ) {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
        $this->renderer = $renderer;
        $this->outputFormats = $outputFormats;
    }

    public function transform(ProjectDescriptor $project, Transformation $transformation): void
    {
        // Feature flag: Guides are disabled by default since this is an experimental feature
        if (!($project->getSettings()->getCustom()[self::FEATURE_FLAG])) {
            return;
        }

        $this->logger->warning(
            'Generating guides is experimental, no BC guarantees are given, use at your own risk'
        );

        /** @var VersionDescriptor $version */
        foreach ($project->getVersions() as $version) {
            foreach ($version->getDocumentationSets() as $documentationSet) {
                if (!$documentationSet instanceof GuideSetDescriptor) {
                    continue;
                }

                $this->renderDocumentationSet($documentationSet, $project, $transformation);
            }
        }
    }

    public function getDefaultSettings(): array
    {
        return [self::FEATURE_FLAG => false];
    }

    private function renderDocumentationSet(
        GuideSetDescriptor $documentationSet,
        ProjectDescriptor $project,
        Transformation $transformation
    ): void {
        $dsn = $documentationSet->getSource()->dsn();
        $stopwatch = $this->startRenderingSetMessage($dsn);

        $this->renderer->initialize($project, $documentationSet, $transformation);

        $configuration = new Configuration(
            $documentationSet->getInputFormat(),
            $this->outputFormats
        );
        $configuration->setOutputFolder($documentationSet->getOutput());

        $this->commandBus->handle(
            new RenderCommand(
                $documentationSet,
                $configuration,
                $transformation->getTransformer()->destination()
            )
        );

        $this->completedRenderingSetMessage($stopwatch, $dsn);
    }

    private function startRenderingSetMessage(Dsn $dsn): Stopwatch
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('guide');
        $this->logger->info('Rendering guide ' . $dsn);

        return $stopwatch;
    }

    private function completedRenderingSetMessage(Stopwatch $stopwatch, Dsn $dsn): void
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
}
