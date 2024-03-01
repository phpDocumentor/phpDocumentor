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

use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function current;

/**
 * @coversDefaultClass \phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder\ApiSetBuilder
 * @covers ::<private>
 * @covers ::__construct
 */
final class ApiSetBuilderTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    private Router|ObjectProphecy $router;
    private ApiSetBuilder $builder;

    protected function setUp(): void
    {
        $this->router = $this->givenARouterThatAlwaysReturnsTheFqsen();
        $this->builder = new ApiSetBuilder($this->router->reveal());
    }

    /** @covers ::build */
    public function testApiDocumentationSetNamespacesAreAddedToTOC(): void
    {
        $apiDocumentationSet = $this->givenAnApiDocumentationSetWithNamespaces();

        $this->builder->build($apiDocumentationSet);

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());

        $namespacesToc = $apiDocumentationSet->getTableOfContents()->get('Namespaces');
        self::assertSame('Namespaces', $namespacesToc->getName());
        self::assertCount(1, $namespacesToc->getRoots());

        $rootEntry = current($namespacesToc->getRoots()->getAll());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }

    /** @covers ::build */
    public function testApiDocumentationSetPackagesAreAddedToTOC(): void
    {
        $apiDocumentationSet = $this->givenAnApiDocumentationSetWithPackages();

        $this->builder->build($apiDocumentationSet);

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());

        $packagesTOC = $apiDocumentationSet->getTableOfContents()->get('Packages');
        self::assertSame('Packages', $packagesTOC->getName());
        self::assertCount(1, $packagesTOC->getRoots());

        $rootEntry = current($packagesTOC->getRoots()->getAll());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }

    private function givenARouterThatAlwaysReturnsTheFqsen(): Router|ObjectProphecy
    {
        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())
            ->will(static fn ($args) => (string) $args[0]->getFullyQualifiedStructuralElementName());

        return $router;
    }

    private function givenAnApiDocumentationSetWithNamespaces(): ApiSetDescriptor
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $apiDocumentationSet->getNamespace()->addChild($this->faker()->namespaceDescriptorTree());

        return $apiDocumentationSet;
    }

    private function givenAnApiDocumentationSetWithPackages(): ApiSetDescriptor
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $apiDocumentationSet->getPackage()->addChild($this->faker()->namespaceDescriptorTree());

        return $apiDocumentationSet;
    }
}
