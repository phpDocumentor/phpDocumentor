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
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

#[CoversClass(TableOfContentsBuilder::class)]
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

    public function testGetDescription(): void
    {
        $description = $this->builder->getDescription();

        $this->assertSame('Builds table of contents for documentation sets', $description);
    }

    public function testPassingAnythingElseThanAVersionDescriptorIsPassedThroughTransparently(): void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();

        $this->builder->__invoke($apiDocumentationSet);

        self::assertEmpty($apiDocumentationSet->getTableOfContents());
    }
}
