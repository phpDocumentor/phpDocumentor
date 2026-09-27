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

namespace functional\phpDocumentor\issues;

use phpDocumentor\FunctionalTestCase;

/** @link https://github.com/phpDocumentor/phpDocumentor/issues/1872 */
final class Issue1872FunctionalTest extends FunctionalTestCase
{
    public function testLogFileIsWrittenWhenLogOptionIsProvided(): void
    {
        $this->runPHPDocWithFile(
            __DIR__ . '/../../assets/core/issues/issue-1872/issue-1872.php',
            ['--log', 'phpdoc.log'],
        );

        $contents = $this->loadContents('phpdoc.log');
        self::assertNotSame('', $contents, 'Expected --log target to receive log records');
        self::assertStringContainsString('Collecting files from', $contents);
    }
}
