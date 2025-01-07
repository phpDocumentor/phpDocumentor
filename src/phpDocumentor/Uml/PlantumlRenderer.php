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

namespace phpDocumentor\Uml;

use Phar;
use phpDocumentor\Guides\Graphs\Renderer\DiagramRenderer;
use phpDocumentor\Guides\RenderContext;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

use function array_merge;
use function file_get_contents;
use function file_put_contents;
use function str_starts_with;
use function sys_get_temp_dir;
use function tempnam;
use function trim;

final class PlantumlRenderer implements DiagramRenderer
{
    public function __construct(private readonly LoggerInterface $logger, private readonly string $plantUmlBinaryPath)
    {
    }

    public function render(RenderContext $renderContext, string $diagram): string|null
    {
        $output = <<<PUML
@startuml
   skinparam ArrowColor #516f42
   skinparam activityBorderColor #516f42
   skinparam activityBackgroundColor #ffffff
   skinparam activityDiamondBorderColor #516f42
   skinparam activityDiamondBackgroundColor #ffffff
   skinparam shadowing false

$diagram
@enduml
PUML;

        if (Phar::running(false) && str_starts_with($this->plantUmlBinaryPath, 'phar://')) {
            $this->logger->warning(<<<'MSG'
You are running phpDocumentor as phar. PlantUML needs to be installed separately.
https://docs.phpdoc.org/guide/guides/generate-diagrams.html
MSG,);

            return null;
        }

        $pumlFileLocation = tempnam(sys_get_temp_dir() . '/phpdocumentor', 'pu_');
        file_put_contents($pumlFileLocation, $output);
        try {
            $process = Process::fromShellCommandline(
                $this->plantUmlBinaryPath . ' -Playout=smetana  -tsvg ' . $pumlFileLocation,
                __DIR__,
                null,
                null,
                600.0,
            );
            $process->run();

            if (! $process->isSuccessful()) {
                $this->logger->error(
                    'Generating the class diagram failed: {error}',
                    array_merge(
                        ['error' => trim($process->getErrorOutput())],
                        $renderContext->getLoggerInformation(),
                    ),
                );

                return null;
            }
        } catch (RuntimeException $e) {
            $this->logger->error(
                'Generating the class diagram failed: {error}',
                array_merge(
                    ['error' => trim($e->getMessage())],
                    $renderContext->getLoggerInformation(),
                ),
            );

            return null;
        }

        return file_get_contents($pumlFileLocation . '.svg') ?: null;
    }
}
