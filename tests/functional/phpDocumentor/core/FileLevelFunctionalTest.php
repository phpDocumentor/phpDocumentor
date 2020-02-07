<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use FilesystemIterator;
use phpDocumentor\Descriptor\FileDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use PHPUnit\Framework\TestCase;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Process\Process;

final class FileLevelFunctionalTest extends TestCase
{
    private $workingDir;

    protected function setUp() : void
    {
        $this->workingDir = tempnam(sys_get_temp_dir(), 'phpdoc');
        unlink($this->workingDir);
        mkdir($this->workingDir);
    }

    protected function tearDown() : void
    {
        $di = new RecursiveDirectoryIterator($this->workingDir, FilesystemIterator::SKIP_DOTS);
        $ri = new RecursiveIteratorIterator($di, RecursiveIteratorIterator::CHILD_FIRST);
        foreach ($ri as $file) {
            $file->isDir() ? rmdir((string)$file) : unlink((string)$file);
        }
    }

    /**
     * @dataProvider emptishFileProvider
     */
    public function testEmptyPHPFileContainsFileLevelDocBlock(string $file) : void
    {
        $this->runPHPDocWithFile($file)->getOutput($file);
        $project = $this->loadAst();

        $this->assertCount(1, $project->getFiles());
        $this->assertFileSummary('This file is part of phpDocumentor.',  $project->getFiles()['test.php']);
    }

    public function emptishFileProvider() : array
    {
        return [
            [
                __DIR__ . '/../../assets/core/fileLevel/emptyFile.php'
            ],
            [
                __DIR__ . '/../../assets/core/fileLevel/requireOnly.php'
            ],
            [
                __DIR__ . '/../../assets/core/fileLevel/shebang.php'
            ]
        ];
    }

    private function runPHPDocWithFile(string $string) : Process
    {
        copy($string, $this->workingDir . '/test.php');

        $process = new Process(
            [
                PHP_BINARY,
                __DIR__ . '/../../../../bin/phpdoc',
                '-vvv',
                '--force',
                '--filename=test.php',
            ],
            $this->workingDir
        );

        return $process->mustRun();
    }

    private function loadAst() : ProjectDescriptor
    {
        return unserialize(file_get_contents($this->workingDir . '/ast.dump'));
    }

    private function assertFileSummary(string $string, FileDescriptor $file)
    {
        self::assertSame($string, $file->getSummary());
    }
}
