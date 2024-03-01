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

namespace phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder;

use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder\GuideSetBuilder
 * @covers ::<private>
 * @covers ::__construct
 */
final class GuideSetBuilderTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    private Router|ObjectProphecy $router;
    private GuideSetBuilder $builder;

    protected function setUp(): void
    {
        $this->router = $this->givenARouterThatAlwaysReturnsTheFqsen();
        $this->builder = new GuideSetBuilder($this->router->reveal());
    }

    /** @covers ::supports */
    public function testItSupportsApiSetDescriptors(): void
    {
        self::assertTrue($this->builder->supports($this->faker()->guideSetDescriptor()));
        self::assertFalse($this->builder->supports($this->faker()->apiSetDescriptor()));
    }

    private function givenARouterThatAlwaysReturnsTheFqsen(): Router|ObjectProphecy
    {
        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())
            ->will(static fn ($args) => (string) $args[0]->getFullyQualifiedStructuralElementName());

        return $router;
    }
}
