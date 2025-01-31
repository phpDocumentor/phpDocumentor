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

use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\Glob;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;
use phpDocumentor\FileSystem\SpecificationFactory;
use PHPUnit\Framework\TestCase;

/** @coversDefaultClass \phpDocumentor\FileSystem\SpecificationFactory */
final class SpecificationFactoryTest extends TestCase
{
    private SpecificationFactory $fixture;

    protected function setUp(): void
    {
        $this->fixture = new SpecificationFactory();
    }

    public function testCreateIgnoreHidden(): void
    {
        $specification = $this->fixture->create(
            ['/some/path/**/*', '/a/second/path/**/*'],
            ['hidden' => true],
            ['php', 'php3'],
        );

        $this->assertEquals(
            new AndSpecification(
                new AndSpecification(
                    new HasExtension(['php', 'php3']),
                    new NotSpecification(
                        new IsHidden(),
                    ),
                ),
                new OrSpecification(
                    new Glob('/some/path/**/*'),
                    new Glob('/a/second/path/**/*'),
                ),
            ),
            $specification,
        );
    }

    public function testCreateIgnorePath(): void
    {
        $specification = $this->fixture->create(
            ['/src/'],
            ['paths' => ['/src/some/path', '/src/some/other/path']],
            ['php'],
        );

        $this->assertEquals(
            new AndSpecification(
                new AndSpecification(
                    new HasExtension(['php']),
                    new NotSpecification(
                        new OrSpecification(
                            new Glob('/src/some/path'),
                            new Glob('/src/some/other/path'),
                        ),
                    ),
                ),
                new Glob('/src/'),
            ),
            $specification,
        );
    }

    public function testNoPaths(): void
    {
        $specification = $this->fixture->create([], ['paths' => ['/src/some/path']], ['php']);

        $this->assertEquals(
            new AndSpecification(
                new HasExtension(['php']),
                new NotSpecification(
                    new Glob('/src/some/path'),
                ),
            ),
            $specification,
        );
    }

    public function testNoIgnore(): void
    {
        $specification = $this->fixture->create(['/src/'], ['paths' => []], ['php']);

        $this->assertEquals(
            new AndSpecification(
                new HasExtension(['php']),
                new Glob('/src/'),
            ),
            $specification,
        );
    }

    public function testInPathMustBeOfTheTypeString(): void
    {
        $specification = $this->fixture->create(
            [
                '/PHPCompatibility/*',
                '/PHPCompatibility/Sniffs/',
            ],
            ['paths' => []],
            ['php'],
        );

        $this->assertEquals(
            new AndSpecification(
                new HasExtension(['php']),
                new OrSpecification(
                    new Glob('/PHPCompatibility/*'),
                    new Glob('/PHPCompatibility/Sniffs/'),
                ),
            ),
            $specification,
        );
    }
}
