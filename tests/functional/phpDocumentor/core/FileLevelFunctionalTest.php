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

namespace functional\phpDocumentor\core;

use phpDocumentor\Descriptor\ApiSetDescriptor;
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

        $versions = $project->getVersions();
        $this->assertCount(1, $versions);

        $apiSets = $versions->first()->getDocumentationSets()->filter(ApiSetDescriptor::class);
        $this->assertCount(1, $apiSets);

        /** @var ApiSetDescriptor $apiSet */
        $apiSet = $apiSets->first();
        $this->assertCount(1, $apiSet->getFiles());

        $this->assertFileSummary('This file is part of phpDocumentor.',  $apiSet->getFiles()['test.php']);
    }

    public static function emptishFileProvider() : array
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
