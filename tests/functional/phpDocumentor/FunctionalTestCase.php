<?php

declare(strict_types=1);

namespace phpDocumentor;

use FilesystemIterator;
use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;

class FunctionalTestCase extends TestCase
{
    private $workingDir;

    protected function setUp() : void
    {
        $this->workingDir = tempnam(sys_get_temp_dir(), 'phpdoc');
        unlink($this->workingDir);
        if (!mkdir($concurrentDirectory = $this->workingDir) && !is_dir($concurrentDirectory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $concurrentDirectory));
        }
    }

    protected function tearDown() : void
    {
        $di = new RecursiveDirectoryIterator($this->workingDir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir((string)$file) : unlink((string)$file);
        }
    }

    protected function runPHPDocWithFile(string $string, array $arguments = []) : Process
    {
        copy($string, $this->workingDir . '/test.php');

        $process = new Process(
            array_merge(
                [
                PHP_BINARY,
                __DIR__ . '/../../../bin/phpdoc',
                '-vvv',
                '--config=none',
                '--force',
                '--filename=test.php',
                ],
                $arguments
            ),
            $this->workingDir
        );

        return $process->mustRun();
    }

    protected function loadAst() : ProjectDescriptor
    {
        return unserialize(file_get_contents($this->workingDir . '/ast.dump'));
    }

}
