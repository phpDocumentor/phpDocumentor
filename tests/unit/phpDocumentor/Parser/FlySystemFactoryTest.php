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

namespace phpDocumentor\Parser;

use phpDocumentor\FileSystem\Dsn;
use phpDocumentor\FileSystem\FileSystem;
use phpDocumentor\FileSystem\FlySystemFactory;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

use function sys_get_temp_dir;

/** @coversDefaultClass \phpDocumentor\FileSystem\FlySystemFactory */
final class FlySystemFactoryTest extends TestCase
{
    use ProphecyTrait;

    private FlySystemFactory $fixture;

    private Dsn $dsn;

    protected function setUp(): void
    {
        $this->dsn = Dsn::createFromString(sys_get_temp_dir());
        $this->fixture = new FlySystemFactory();
    }

    public function testCreateLocalFilesystemWithoutCache(): void
    {
        $result = $this->fixture->create($this->dsn);

        $this->assertInstanceOf(FileSystem::class, $result);
    }

    public function testUnsupportedScheme(): void
    {
        $this->expectException('InvalidArgumentException');
        $dsn = Dsn::createFromString('git+http://github.com');

        $this->fixture->create($dsn);
    }
}
