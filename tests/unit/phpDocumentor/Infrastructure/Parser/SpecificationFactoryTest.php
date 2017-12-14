<?php
/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @copyright 2010-2015 Mike van Riel<mike@phpdoc.org>
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Infrastructure\Parser;

use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;
use phpDocumentor\Infrastructure\Parser\SpecificationFactory;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Infrastructure\Parser\SpecificationFactory
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
        $paths = array(
            'some/path',
        );

        $ignore = array(
            'hidden' => true,
        );

        $extensions = array(
            'php'
        );

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
        $paths = array(
            'src/',
        );

        $ignore = array(
            'paths' => ['src/some/path', 'src/some/other/path'],
        );

        $extensions = array(
            'php'
        );

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
        $paths = array(
            'src/',
        );

        $ignore = array(
            'paths' => [],
        );

        $extensions = array(
            'php'
        );

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
