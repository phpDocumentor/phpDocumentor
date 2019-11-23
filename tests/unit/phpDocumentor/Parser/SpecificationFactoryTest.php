<?php declare(strict_types=1);
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2018 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Parser;

use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Parser\SpecificationFactory
 * @covers ::create
 * @covers ::<private>
 */
final class SpecificationFactoryTest extends TestCase
{
    /** @var SpecificationFactory */
    private $fixture;

    protected function setUp(): void
    {
        $this->fixture = new SpecificationFactory();
    }

    public function testCreateIgnoreHidden() : void
    {
        $specification = $this->fixture->create(['some/path', 'a/second/path'], ['hidden' => true], ['php', 'php3']);

        $this->assertEquals(
            new AndSpecification(
                new OrSpecification(
                    new InPath(new Path('some/path')),
                    new InPath(new Path('a/second/path'))
                ),
                new AndSpecification(
                    new HasExtension(['php', 'php3']),
                    new NotSpecification(
                        new IsHidden()
                    )
                )
            ),
            $specification
        );
    }

    public function testCreateIgnorePath() : void
    {
        $specification = $this->fixture->create(
            ['src/'],
            ['paths' => ['src/some/path', 'src/some/other/path']],
            ['php']
        );

        $this->assertEquals(
            new AndSpecification(
                new InPath(new Path('src/')),
                new AndSpecification(
                    new HasExtension(['php']),
                    new NotSpecification(
                        new OrSpecification(
                            new InPath(new Path('src/some/path')),
                            new InPath(new Path('src/some/other/path'))
                        )
                    )
                )
            ),
            $specification
        );
    }

    public function testNoPaths() : void
    {
        $specification = $this->fixture->create([], ['paths' => ['src/some/path']], ['php']);

        $this->assertEquals(
            new AndSpecification(
                new HasExtension(['php']),
                new NotSpecification(
                    new InPath(new Path('src/some/path'))
                )
            ),
            $specification
        );
    }

    public function testNoIgnore() : void
    {
        $specification = $this->fixture->create(['src/'], ['paths' => []], ['php']);

        $this->assertEquals(
            new AndSpecification(
                new InPath(new Path('src/')),
                new HasExtension(['php'])
            ),
            $specification
        );
    }
}
