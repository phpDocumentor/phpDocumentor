<?php
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
use phpDocumentor\Parser\SpecificationFactoryInterface;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Parser\SpecificationFactory
 * @covers ::create
 * @covers ::<private>
 */
class SpecificationFactoryTest extends TestCase
{
    /**
     * @var SpecificationFactory
     */
    private $fixture;

    protected function setUp()
    {
        $this->fixture = new SpecificationFactory();
    }

    public function testCreateIgnoreHidden()
    {
        $paths = [
            'some/path',
        ];

        $ignore = [
            'hidden' => true,
        ];

        $extensions = [
            'php',
        ];

        $specification = $this->fixture->create($paths, $ignore, $extensions);

        $this->assertEquals(
            new AndSpecification(
                new InPath(new Path('some/path')),
                new AndSpecification(
                    new HasExtension(['php']),
                    new NotSpecification(
                        new IsHidden()
                    )
                )
            ),
            $specification
        );
    }

    public function testCreateIgnorePath()
    {
        $paths = [
            'src/',
        ];

        $ignore = [
            'paths' => ['src/some/path', 'src/some/other/path'],
        ];

        $extensions = [
            'php',
        ];

        $specification = $this->fixture->create($paths, $ignore, $extensions);

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

    public function testNoIgnore()
    {
        $paths = [
            'src/',
        ];

        $ignore = [
            'paths' => [],
        ];

        $extensions = [
            'php',
        ];

        $specification = $this->fixture->create($paths, $ignore, $extensions);

        $this->assertEquals(
            new AndSpecification(
                new InPath(new Path('src/')),
                new HasExtension(['php'])
            ),
            $specification
        );
    }
}
