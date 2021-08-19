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

namespace phpDocumentor\FlowService\Guide;

use InvalidArgumentException;
use League\Tactician\CommandBus;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\GuideSetDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Dsn;
use phpDocumentor\FileSystem\FileSystemFactory;
use phpDocumentor\FlowService\Transformer;
use phpDocumentor\Guides\RenderCommand;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;

use function count;
use function sprintf;

/**
 * @experimental this feature is in alpha stages and can have unresolved issues or missing features.
 */
final class RenderGuide implements Transformer, ProjectDescriptor\WithCustomSettings
{
    public const FEATURE_FLAG = 'guides.enabled';

    /** @var LoggerInterface */
    private $logger;

    /** @var CommandBus */
    private $commandBus;

    /** @var FileSystemFactory */
    private $fileSystems;
    private EnvironmentFactory $environmentFactory;
    private EnvironmentBuilder $environmentBuilder;

    public function __construct(
        LoggerInterface $logger,
        CommandBus $commandBus,
        FileSystemFactory $fileSystems,
        EnvironmentFactory $environmentFactory,
        EnvironmentBuilder $environmentBuilder
    ) {
        $this->logger = $logger;
        $this->commandBus = $commandBus;
        $this->fileSystems = $fileSystems;
        $this->environmentFactory = $environmentFactory;
        $this->environmentBuilder = $environmentBuilder;
    }

    public function getName(): string
    {
        return 'RenderGuide';
    }

    public function execute(ProjectDescriptor $project, DocumentationSetDescriptor $documentationSet, Template $template): void
    {
        if (!$documentationSet instanceof GuideSetDescriptor) {
            throw new InvalidArgumentException('Invalid documentation set');
        }

        $this->logger->warning(
            'Generating guides is experimental, no BC guarantees are given, use at your own risk'
        );

        $dsn = $documentationSet->getSource()->dsn();
        $stopwatch = $this->startRenderingSetMessage($dsn);

        $this->environmentBuilder->setEnvironmentFactory(
            function () use ($template, $project, $documentationSet) {
                $twig = $this->environmentFactory->create($project, $template);
                $twig->addGlobal('project', $project);
                $twig->addGlobal('usesNamespaces', count($project->getNamespace()->getChildren()) > 0);
                $twig->addGlobal('usesPackages', count($project->getPackage()->getChildren()) > 0);
                $twig->addGlobal('documentationSet', $documentationSet);
                $twig->addGlobal('destinationPath', null);

                return $twig;
            }
        );

        $this->commandBus->handle(
            new RenderCommand(
                $documentationSet,
                $this->fileSystems->create($dsn),
                $this->fileSystems->createDestination($documentationSet)
            )
        );

        $this->completedRenderingSetMessage($stopwatch, $dsn);
    }

    public function getDefaultSettings() : array
    {
        return [self::FEATURE_FLAG => false];
    }

    private function startRenderingSetMessage(Dsn $dsn) : Stopwatch
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
