<?php declare(strict_types=1);
/**
 * phpDocumentor
 *
 * PHP Version 5.3
 *
 * @copyright 2010-2018 Mike van Riel / Naenius (http://www.naenius.com)
 * @license   http://www.opensource.org/licenses/mit-license.php MIT
 * @link      http://phpdoc.org
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
    protected function setUp(): void
    {
        $this->elementCollection = new Collection();

        $urlGenerator = m::mock(UrlGeneratorInterface::class);
        $converter = new QualifiedNameToUrlConverter();
        $builder = m::mock('phpDocumentor\Descriptor\ProjectDescriptorBuilder');
        $builder
            ->shouldReceive('getProjectDescriptor->getIndexes->get')
            ->with('elements')
            ->andReturn($this->elementCollection);

        $this->fixture = new Router(
            $builder,
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
