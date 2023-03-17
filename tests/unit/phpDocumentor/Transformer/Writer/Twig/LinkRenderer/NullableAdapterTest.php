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

use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Null_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer\NullableAdapter
 * @covers ::<private>
 * @covers ::__construct
 */
final class NullableAdapterTest extends TestCase
{
    use ProphecyTrait;

    /** @var LinkRendererInterface|ObjectProphecy  */
    private ObjectProphecy $linkRenderer;
    private NullableAdapter $adapter;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRendererInterface::class);

        // pre-loaded expectations for the data provider
        $this->linkRenderer->render(new String_(), Argument::any())->willReturn('string');
        $this->linkRenderer->render(new Null_(), Argument::any())->willReturn('null');
        $this->linkRenderer
            ->render(new Compound([new Boolean(), new String_()]), Argument::any())
            ->willReturn(['bool', 'string']);

        $this->adapter = new NullableAdapter(
            $this->linkRenderer->reveal()
        );
    }

    /**
     * @covers ::supports
     */
    public function testItSupportsNullableTypes(): void
    {
        self::assertTrue($this->adapter->supports(new Nullable(new String_())));
        self::assertFalse($this->adapter->supports(new String_()));
    }

    /**
     * @covers ::render
     */
    public function testRenderOnlyAcceptsNullableElements(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter->render(new String_(), LinkRenderer::PRESENTATION_NORMAL);
    }

    /**
     * @covers ::render
     * @dataProvider renderingVariations
     */
    public function testRenderProducesExpectedOutputBasedOn(Nullable $value, iterable $expected): void
    {
        self::assertSame($expected, $this->adapter->render($value, LinkRenderer::PRESENTATION_NORMAL));
    }

    /**
     * @return array<string, list<iterable>>
     */
    public function renderingVariations(): array
    {
        return [
            'Converts nullable to array with type and null' => [
                new Nullable(new String_()),
                ['string', 'null'],
            ],
            'Flattens results that produce arrays themselves' => [
                new Nullable(new Compound([new Boolean(), new String_()])),
                ['bool', 'string', 'null'],
            ],
        ];
    }
}
