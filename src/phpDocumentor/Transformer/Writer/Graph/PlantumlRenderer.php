<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Graph;

use Psr\Log\LoggerInterface;
use Symfony\Component\Process\Process;

final class PlantumlRenderer
{
    /** @var LoggerInterface */
    private $logger;

    /** @var string */
    private $plantUmlBinaryPath;

    public function __construct(LoggerInterface $logger, string $plantUmlBinaryPath)
    {
        $this->logger = $logger;
        $this->plantUmlBinaryPath = $plantUmlBinaryPath;
    }

    public function render(string $diagram): ?string
    {
        $pumlFileLocation = tempnam('phpdocumentor', 'pu_');

        $output = <<<PUML
@startuml
$diagram
@enduml
PUML;
        file_put_contents($pumlFileLocation, $output);

        $process = new Process([$this->plantUmlBinaryPath, '-tsvg', $pumlFileLocation], __DIR__, null, null, 600.0);
        $process->run();

        if (!$process->isSuccessful()) {
            $this->logger->error('Generating the class diagram failed', ['error' => $process->getErrorOutput()]);
            return null;
        }

        return file_get_contents($pumlFileLocation . '.svg') ?: null;
    }
}