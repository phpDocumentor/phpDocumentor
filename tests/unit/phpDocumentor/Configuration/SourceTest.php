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

namespace phpDocumentor\Configuration;

use BadMethodCallException;
use phpDocumentor\Faker\Faker;
use phpDocumentor\FileSystem\Path;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Configuration\Source */
final class SourceTest extends TestCase
{
    use Faker;

    /** @uses \phpDocumentor\FileSystem\Dsn */
    public function testSourceReturnsDsn(): void
    {
        $dsn = self::faker()->dsn();
        $source = new Source($dsn, []);

        self::assertSame($dsn, $source->dsn());
    }

    /** @uses \phpDocumentor\FileSystem\Dsn */
    public function testWithDsnReturnsNewInstanceOfSource(): void
    {
        $dsn = self::faker()->dsn();
        $source = new Source($dsn, []);

        $newSource = $source->withDsn($dsn);

        self::assertEquals($newSource, $source);
        self::assertNotSame($newSource, $source);
    }

    /** @uses \phpDocumentor\FileSystem\Dsn */
    public function testWithDsnReturnsSetsNewDsn(): void
    {
        $dsn = self::faker()->dsn();
        $source = new Source($dsn, []);

        $newDsn = self::faker()->dsn();
        $newSource = $source->withDsn($newDsn);

        self::assertSame($newDsn, $newSource->dsn());
        self::assertSame($dsn, $source->dsn());
    }

    public function testSourceImplementsArrayAccess(): void
    {
        $dsn = self::faker()->dsn();
        $paths = [
            self::faker()->path(),
            self::faker()->path(),
        ];

        $source = new Source($dsn, $paths);

        self::assertSame($dsn, $source['dsn']);
        self::assertSame($paths, $source['paths']);
    }

    public function testSourceArrayAccessIsImmutableCannotSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $dsn   = self::faker()->dsn();
        $paths = [
            self::faker()->path(),
            self::faker()->path(),
        ];

        $source = new Source($dsn, $paths);

        $source['paths'] = [];
    }

    public function testSourceArrayAccessIsImmutableCannotUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $dsn   = self::faker()->dsn();
        $paths = [
            self::faker()->path(),
            self::faker()->path(),
        ];

        $source = new Source($dsn, $paths);

        unset($source['paths']);
    }

    /**
     * @param list<string> $globs
     *
     * @dataProvider pathProvider
     */
    public function testSourceGlobPathsNormalizesPaths(Path $input, array $globs): void
    {
        $source = new Source(self::faker()->dsn(), [$input]);

        self::assertEquals($globs, $source->globPatterns());
    }

    public static function pathProvider(): array
    {
        return [
            'directory' => [
                new Path('src'),
                ['/src', '/src/**/*'],
            ],
            'current directory' => [
                new Path('.'),
                ['/**/*'],
            ],
            'relative directory' => [
                new Path('./src'),
                ['/src', '/src/**/*'],
            ],
            'glob with trailing wildcard' => [
                new Path('/src/*'),
                ['/src/*'],
            ],
            'single file' => [
                new Path('src/dir/test.php'),
                ['/src/dir/test.php', '/src/dir/test.php/**/*'],
            ],
            'directory containing a dot' => [
                new Path('test.1'),
                ['/test.1', '/test.1/**/*'],
            ],
        ];
    }
}
