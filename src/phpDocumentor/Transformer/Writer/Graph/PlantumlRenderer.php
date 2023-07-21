<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Graph;

use Phar;
use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

use function file_get_contents;
use function file_put_contents;
use function tempnam;

class PlantumlRenderer
{
    public function __construct(private readonly LoggerInterface $logger, private string $plantUmlBinaryPath)
    {
    }

    public function render(string $diagram): string|null
    {
        $pumlFileLocation = tempnam('phpdocumentor', 'pu_');
        if ($pumlFileLocation === false) {
            return null;
        }

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
        file_put_contents($pumlFileLocation, $output);

        if (Phar::running() !== '') {
            $this->plantUmlBinaryPath = 'plantuml';
        }

        $process = new Process([$this->plantUmlBinaryPath, '-tsvg', $pumlFileLocation], __DIR__, null, null, 1200.0);
        $process->run();

        if (! $process->isSuccessful()) {
            $this->logger->error('Generating the class diagram failed', ['error' => $process->getErrorOutput()]);

            return null;
        }

        return file_get_contents($pumlFileLocation . '.svg') ?: null;
    }
}
