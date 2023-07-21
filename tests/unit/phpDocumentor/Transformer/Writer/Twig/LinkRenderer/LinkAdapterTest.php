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

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use ArrayObject;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

final class LinkAdapterTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @var LinkRendererInterface|ObjectProphecy */
    private ObjectProphecy $linkRenderer;

    /** @var UrlGenerator|ObjectProphecy */
    private ObjectProphecy $urlGenerator;

    /** @var HtmlFormatter|ObjectProphecy */
    private ObjectProphecy $htmlFormatter;

    private LinkAdapter $adapter;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRenderer::class);
        $this->urlGenerator = $this->prophesize(UrlGenerator::class);
        $this->htmlFormatter = $this->prophesize(HtmlFormatter::class);

        $this->linkRenderer->getDocumentationSet()->willReturn($this->faker()->apiSetDescriptor());

        // pre-loaded expectations for the data provider
        $this->urlGenerator
            ->generate('http://example.org', 'http://example.org')
            ->willReturn('http://example.org');
        $this->urlGenerator
            ->generate('doc://getting-started/index', 'doc://getting-started/index')
            ->willReturn('doc://getting-started/index');

        $this->adapter = new LinkAdapter(
            $this->linkRenderer->reveal(),
            $this->urlGenerator->reveal(),
            $this->htmlFormatter->reveal(),
        );
    }

    /**
     * @covers ::supports()
     */
    public function testItSupportsAnyType(): void
    {
        self::assertTrue($this->adapter->supports('http://example.org'));
        self::assertTrue($this->adapter->supports('doc://getting-started/index'));
        self::assertTrue($this->adapter->supports([new String_()]));
        self::assertTrue($this->adapter->supports(new ArrayObject([])));
        self::assertTrue($this->adapter->supports(new String_()));
        self::assertTrue($this->adapter->supports(new Fqsen('\MyAwesomeObject')));
        self::assertTrue($this->adapter->supports('\MyAwesomeObject'));
        self::assertTrue($this->adapter->supports(new ClassDescriptor()));
    }

    /**
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @covers ::render()
     * @dataProvider renderingVariations
     */
    public function testRenderProducesExpectedOutputBasedOn($value, Target $expectedTarget): void
    {
        $this->htmlFormatter->format($expectedTarget)->shouldBeCalledOnce()->willReturn('HTML');

        self::assertSame('HTML', $this->adapter->render($value, LinkRenderer::PRESENTATION_NORMAL));
    }

    /**
     * @return array<string, list<iterable>>
     */
    public function renderingVariations(): array
    {
        $presentation = LinkRenderer::PRESENTATION_NORMAL;

        return [
            [
                'http://example.org',
                new Target('http://example.org', 'http://example.org', $presentation),
            ],
            [
                'doc://getting-started/index',
                new Target('doc://getting-started/index', 'doc://getting-started/index', $presentation),
            ],
        ];
    }
}
