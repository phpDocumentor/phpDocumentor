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

namespace phpDocumentor\Compiler\Version\Pass;

use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/** @coversDefaultClass \phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder */
final class TableOfContentsBuilderTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    private Router|ObjectProphecy $router;
    private TableOfContentsBuilder $builder;

    protected function setUp(): void
    {
        $this->builderAdapter = $this->prophesize(TableOfContentsBuilder\DocumentationSetBuilder::class);

        $this->builder = new TableOfContentsBuilder([$this->builderAdapter->reveal()]);
    }

    /** @covers ::getDescription */
    public function testGetDescription(): void
    {
        $description = $this->builder->getDescription();

        $this->assertSame('Builds table of contents for documentation sets', $description);
    }

    /** @covers ::__invoke */
    public function testPassingAnythingElseThanAVersionDescriptorIsPassedThroughTransparently(): void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();

        $this->builder->__invoke($apiDocumentationSet);

        self::assertEmpty($apiDocumentationSet->getTableOfContents());
    }
}
