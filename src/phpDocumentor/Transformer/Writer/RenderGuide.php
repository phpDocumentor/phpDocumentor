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
use phpDocumentor\Descriptor\Interfaces\DocumentationSetInterface;
use phpDocumentor\Descriptor\Interfaces\DocumentInterface;
use phpDocumentor\Descriptor\Interfaces\GuideDocumentationSet;
use phpDocumentor\Descriptor\Interfaces\ProjectInterface;
use phpDocumentor\FileSystem\Dsn;
use phpDocumentor\FileSystem\FlySystemFactory;
use phpDocumentor\Guides\Handlers\RenderCommand;
use phpDocumentor\Guides\RenderContext;
use phpDocumentor\Guides\TemplateRenderer;
use phpDocumentor\Transformer\Template;
use phpDocumentor\Transformer\Transformation;
use phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory;
use phpDocumentor\WithCustomSettings;
use Psr\Log\LoggerInterface;
use Symfony\Component\Stopwatch\Stopwatch;
use Twig\Environment;
use Twig\Error\LoaderError;

use function array_map;
use function sprintf;

/** @experimental this feature is in alpha stages and can have unresolved issues or missing features. */
final class RenderGuide extends WriterAbstract implements
    WithCustomSettings,
    Initializable,
    TemplateRenderer
{
    private Environment $environment;

    public function __construct(
        private readonly LoggerInterface $logger,
        private readonly CommandBus $commandBus,
        private readonly FlySystemFactory $flySystemFactory,
        private readonly EnvironmentFactory $environmentFactory,
    ) {
    }

    public function getName(): string
    {
        return 'RenderGuide';
    }

    public function initialize(
        ProjectInterface $project,
        DocumentationSetInterface $documentationSet,
        Template $template,
    ): void {
        $this->environment = $this->environmentFactory->create($project, $documentationSet, $template);
    }

    public function transform(
        Transformation $transformation,
        ProjectInterface $project,
        DocumentationSetInterface $documentationSet,
    ): void {
        if ($documentationSet instanceof GuideSetDescriptor === false) {
            return;
        }

        $this->renderDocumentationSet($documentationSet, $transformation);
    }

    public function getDefaultSettings(): array
    {
        return [];
    }

    private function renderDocumentationSet(
        GuideDocumentationSet $documentationSet,
        Transformation $transformation,
    ): void {
        $dsn = $documentationSet->getSource()->dsn();
        $stopwatch = $this->startRenderingSetMessage($dsn);
        $destination = $transformation->getTransformer()->destination();
        $documents = array_map(
            static fn (DocumentInterface $dd) => $dd->getDocumentNode(),
            $documentationSet->getDocuments()->getAll(),
        );

        $command = new RenderCommand(
            $documentationSet->getOutputFormat(),
            $documents,
            $this->flySystemFactory->create($dsn),
            $destination,
            $documentationSet->getGuidesProjectNode(),
            $documentationSet->getOutputLocation(),
        );

        $this->commandBus->handle($command);

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
                $stopwatchEvent->getMemory() / 1024 / 1024,
            ),
        );
    }

    public function renderTemplate(RenderContext $context, string $template, array $params = []): string
    {
        $this->environment->addGlobal(
            'destinationPath',
            $context->getDestinationPath() . '/' . $context->getCurrentFileName(),
        );
        $this->environment->addGlobal('env', $context);

        return $this->environment->render($template, $params);
    }

    public function isTemplateFound(RenderContext $context, string $template): bool
    {
        try {
            $this->environment->load($template);

            return true;
        } catch (LoaderError) {
        }

        return false;
    }
}
