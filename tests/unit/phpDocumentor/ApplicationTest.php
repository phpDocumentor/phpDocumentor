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

namespace phpDocumentor;

use PHPUnit\Framework\TestCase;

use function file_get_contents;
use function trim;

/**
 * @coversDefaultClass \phpDocumentor\Application
 * @covers ::__construct
 * @covers ::<private>
 */
final class ApplicationTest extends TestCase
{
    /**
     * @covers ::VERSION
     */
    public function testItReturnsTheVersionNumberFromTheVersionFile(): void
    {
        $this->assertSame(trim(file_get_contents(__DIR__ . '/../../../VERSION')), Application::VERSION());
    }
}
