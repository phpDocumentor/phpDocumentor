<?php

declare(strict_types=1);

namespace phpDocumentor\Configuration;

use BadMethodCallException;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Path;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\Configuration\Source */
final class SourceTest extends TestCase
{
    use Faker;

    /**
     * @uses \phpDocumentor\Dsn
     *
     * @covers ::__construct
     * @covers ::dsn
     */
    public function testSourceReturnsDsn(): void
    {
        $dsn = $this->faker()->dsn();
        $source = new Source($dsn, []);

        self::assertSame($dsn, $source->dsn());
    }

    /**
     * @uses \phpDocumentor\Dsn
     *
     * @covers ::__construct
     * @covers ::dsn
     * @covers ::withDsn
     */
    public function testWithDsnReturnsNewInstanceOfSource(): void
    {
        $dsn = $this->faker()->dsn();
        $source = new Source($dsn, []);

        $newSource = $source->withDsn($dsn);

        self::assertEquals($newSource, $source);
        self::assertNotSame($newSource, $source);
    }

    /**
     * @uses \phpDocumentor\Dsn
     *
     * @covers ::__construct
     * @covers ::dsn
     * @covers ::withDsn
     */
    public function testWithDsnReturnsSetsNewDsn(): void
    {
        $dsn = $this->faker()->dsn();
        $source = new Source($dsn, []);

        $newDsn = $this->faker()->dsn();
        $newSource = $source->withDsn($newDsn);

        self::assertSame($newDsn, $newSource->dsn());
        self::assertSame($dsn, $source->dsn());
    }

    /**
     * @covers ::__construct
     * @covers ::offsetGet
     */
    public function testSourceImplementsArrayAccess(): void
    {
        $dsn = $this->faker()->dsn();
        $paths = [
            $this->faker->path(),
            $this->faker->path(),
        ];

        $source = new Source($dsn, $paths);

        self::assertSame($dsn, $source['dsn']);
        self::assertSame($paths, $source['paths']);
    }

    /**
     * @covers ::__construct
     * @covers ::offsetSet
     */
    public function testSourceArrayAccessIsImmutableCannotSet(): void
    {
        $this->expectException(BadMethodCallException::class);
        $dsn   = $this->faker()->dsn();
        $paths = [
            $this->faker->path(),
            $this->faker->path(),
        ];

        $source = new Source($dsn, $paths);

        $source['paths'] = [];
    }

    /**
     * @covers ::__construct
     * @covers ::offsetUnset
     */
    public function testSourceArrayAccessIsImmutableCannotUnset(): void
    {
        $this->expectException(BadMethodCallException::class);
        $dsn   = $this->faker()->dsn();
        $paths = [
            $this->faker->path(),
            $this->faker->path(),
        ];

        $source = new Source($dsn, $paths);

        unset($source['paths']);
    }

    /**
     * @dataProvider pathProvider
     * @covers ::__construct
     * @covers ::globPatterns
     * @covers ::<private>
     */
    public function testSourceGlobPathsNormalizesPaths(Path $input, string $glob): void
    {
        $source = new Source($this->faker()->dsn(), [$input]);

        self::assertEquals([$glob], $source->globPatterns());
    }

    public function pathProvider(): array
    {
        return [
            [
                new Path('src'),
                '/src/**/*',
            ],
            [
                new Path('.'),
                '/**/*',
            ],
            [
                new Path('./src'),
                '/src/**/*',
            ],
            [
                new Path('/src/*'),
                '/src/*',
            ],
            [
                new Path('src/dir/test.php'),
                '/src/dir/test.php',
            ],
        ];
    }
}
