<?php

declare(strict_types=1);

/**
 *  This file is part of phpDocumentor.
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 * @link      https://phpdoc.org
 */

namespace phpDocumentor\Compiler\ApiDocumentation\Pass;

use phpDocumentor\Compiler\Version\Pass\TableOfContentsBuilder;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Descriptor\VersionDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;

use function current;

final class TableOfContentsBuilderTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    public function testApiDocumentationSetNamespacesAreAddedAsTOC(): void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $apiDocumentationSet->getNamespace()->addChild($this->faker()->namespaceDescriptorTree());

        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())->will(fn ($args) => (string) $args[0]->getFullyQualifiedStructuralElementName());
        $pass = new TableOfContentsBuilder($router->reveal(), new NullLogger());
        $pass->__invoke(new VersionDescriptor('1', new Collection([$apiDocumentationSet])));

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());
        /** @var TocDescriptor $namespacesToc */
        $namespacesToc = $apiDocumentationSet->getTableOfContents()->get('Namespaces');
        self::assertSame('Namespaces', $namespacesToc->getName());
        self::assertCount(1, $namespacesToc->getRoots());
        /** @var Entry $rootEntry */
        $rootEntry = current($namespacesToc->getRoots()->getAll());
        //self::assertCount(2, $rootEntry->getChildren());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }

    public function testApiDocumentationSetPackagesAreAddedAsTOC(): void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $apiDocumentationSet->getPackage()->addChild($this->faker()->namespaceDescriptorTree());

        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())->will(fn ($args) => (string) $args[0]->getFullyQualifiedStructuralElementName());
        $pass = new TableOfContentsBuilder($router->reveal());
        $pass->__invoke(new VersionDescriptor('1', new Collection([$apiDocumentationSet])));

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());
        /** @var TocDescriptor $namespacesToc */
        $namespacesToc = $apiDocumentationSet->getTableOfContents()->get('Packages');
        self::assertSame('Packages', $namespacesToc->getName());
        self::assertCount(1, $namespacesToc->getRoots());
        /** @var Entry $rootEntry */
        $rootEntry = current($namespacesToc->getRoots()->getAll());
        //self::assertCount(3, $rootEntry->getChildren());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }
}
