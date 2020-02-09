<?php

declare(strict_types=1);

namespace functional\phpDocumentor\core;

use phpDocumentor\FunctionalTestCase;
use phpDocumentor\Descriptor\FileDescriptor;

final class FileLevelFunctionalTest extends FunctionalTestCase
{
    /**
     * @dataProvider emptishFileProvider
     */
    public function testEmptyPHPFileContainsFileLevelDocBlock(string $file) : void
    {
        $this->runPHPDocWithFile($file);
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

    private function assertFileSummary(string $string, FileDescriptor $file) : void
    {
        self::assertSame($string, $file->getSummary());
    }
}
