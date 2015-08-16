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

namespace phpDocumentor\FlyFinder;
use Flyfinder\Path;
use Flyfinder\Specification\AndSpecification;
use Flyfinder\Specification\HasExtension;
use Flyfinder\Specification\InPath;
use Flyfinder\Specification\IsHidden;
use Flyfinder\Specification\NotSpecification;
use Flyfinder\Specification\OrSpecification;

/**
 * @coversDefaultClass \phpDocumentor\FlyFinder\SpecificationFactory
 * @covers ::create
 * @covers ::<private>
 */
class SpecificationFactoryTest extends \PHPUnit_Framework_TestCase
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
        $ignore = array(
            'hidden' => true,
        );

        $extensions = array(
            'php'
        );

        $specification = $this->fixture->create($ignore, $extensions);

        $this->assertEquals(
            new AndSpecification(
                new NotSpecification(
                    new IsHidden()
                ),
                new HasExtension(['php'])
            ),
            $specification
        );
    }

    public function testCreateIgnorePath()
    {
        $ignore = array(
            'paths' => ['some/path', 'some/other/path'],
        );

        $extensions = array(
            'php'
        );

        $specification = $this->fixture->create($ignore, $extensions);

        $this->assertEquals(
            new AndSpecification(
                new NotSpecification(
                    new OrSpecification(
                        new InPath(new Path('some/path')),
                        new InPath(new Path('some/other/path'))
                    )
                ),
                new HasExtension(['php'])
            ),
            $specification
        );
    }
}

