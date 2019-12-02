<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */
namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Transformer\Router\UrlGenerator\QualifiedNameToUrlConverter;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

final class RouterTest extends MockeryTestCase
{
    /** @var Router */
    private $fixture;

    /** @var Collection */
    private $elementCollection;

    /**
     * Sets up the fixture.
     */
    protected function setUp() : void
    {
        $this->elementCollection = new Collection();

        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $converter    = new QualifiedNameToUrlConverter();

        $this->fixture = new Router(
            new UrlGenerator\FqsenDescriptor($urlGenerator, $converter),
            $converter,
            $urlGenerator
        );
    }

    /**
     * @dataProvider provideDescriptorNames
     */
    public function testIfARouteForAFileCanBeGenerated($descriptorName, $generatorName = null) : void
    {
        $this->markTestIncomplete(
            'The Router has been rewritten so the content of this test was irrelevant; '
            . 'but we still want to test the case and keep it'
        );
    }

    public function testIfARouteForAFqsenFileCanBeGenerated() : void
    {
        $this->markTestIncomplete(
            'The Router has been rewritten so the content of this test was irrelevant; '
            . 'but we still want to test the case and keep it'
        );
    }

    public function testIfARouteForAUrlCanBeGenerated() : void
    {
        $this->markTestIncomplete(
            'The Router has been rewritten so the content of this test was irrelevant; '
            . 'but we still want to test the case and keep it'
        );
    }

    public function testIfARouteForAFqsenCanBeGenerated() : void
    {
        $this->markTestIncomplete(
            'The Router has been rewritten so the content of this test was irrelevant; '
            . 'but we still want to test the case and keep it'
        );
    }

    public function testGeneratingRouteForUnknownNodeReturnsFalse() : void
    {
        $this->markTestIncomplete(
            'The Router has been rewritten so the content of this test was irrelevant; '
            . 'but we still want to test the case and keep it'
        );
    }

    /**
     * Returns the names of descriptors and generators supported by the StandardRouter.
     *
     * @return string[][]
     */
    public function provideDescriptorNames() : array
    {
        return [
            ['FileDescriptor'],
            ['NamespaceDescriptor'],
            ['PackageDescriptor'],
            ['ClassDescriptor'],
            ['InterfaceDescriptor', 'ClassDescriptor'],
            ['TraitDescriptor', 'ClassDescriptor'],
            ['MethodDescriptor'],
            ['FunctionDescriptor'],
            ['PropertyDescriptor'],
            ['ConstantDescriptor'],
        ];
    }
}
