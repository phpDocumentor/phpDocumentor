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

namespace phpDocumentor\Compiler\Pass;

use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\TocDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use function current;

final class TableOfContentsBuilderTest extends TestCase
{
    use Faker;

    public function testApiDocumentationSetNamespacesAreAddedAsTOC() : void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $project             = $this->faker()->projectDescriptor();
        $project->getVersions()->add($this->faker()->versionDescriptor([$apiDocumentationSet]));
        $project->getNamespace()->addChild($this->faker()->namespaceDescriptorTree());

        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())->will(function ($args) {
            return (string) $args[0]->getFullyQualifiedStructuralElementName();
        });
        $pass = new TableOfContentsBuilder($router->reveal());
        $pass->execute($project);

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());
        /** @var TocDescriptor $namespacesToc */
        $namespacesToc = $apiDocumentationSet->getTableOfContents()->get('Namespaces');
        self::assertSame('Namespaces', $namespacesToc->getName());
        self::assertCount(1, $namespacesToc->getRoots());
        /** @var Entry $rootEntry */
        $rootEntry = current($namespacesToc->getRoots()->getAll());
        self::assertCount(3, $rootEntry->getChildren());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }

    public function testApiDocumentationSetPackagesAreAddedAsTOC() : void
    {
        $apiDocumentationSet = $this->faker()->apiSetDescriptor();
        $project             = $this->faker()->projectDescriptor();
        $project->getVersions()->add($this->faker()->versionDescriptor([$apiDocumentationSet]));
        $project->getPackage()->addChild($this->faker()->namespaceDescriptorTree());

        $router = $this->prophesize(Router::class);
        $router->generate(Argument::any())->will(function ($args) {
            return (string) $args[0]->getFullyQualifiedStructuralElementName();
        });
        $pass = new TableOfContentsBuilder($router->reveal());
        $pass->execute($project);

        self::assertCount(1, $apiDocumentationSet->getTableOfContents());
        /** @var TocDescriptor $namespacesToc */
        $namespacesToc = $apiDocumentationSet->getTableOfContents()->get('Packages');
        self::assertSame('Packages', $namespacesToc->getName());
        self::assertCount(1, $namespacesToc->getRoots());
        /** @var Entry $rootEntry */
        $rootEntry = current($namespacesToc->getRoots()->getAll());
        self::assertCount(3, $rootEntry->getChildren());
        foreach ($rootEntry->getChildren() as $child) {
            self::assertSame($rootEntry->getUrl(), $child->getParent());
        }
    }
}
