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
use InvalidArgumentException;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer\IterableAdapter
 * @covers ::<private>
 * @covers ::__construct
 */
final class IterableAdapterTest extends TestCase
{
    use ProphecyTrait;

    /** @var LinkRendererInterface|ObjectProphecy  */
    private ObjectProphecy $linkRenderer;
    private IterableAdapter $adapter;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRendererInterface::class);

        // pre-loaded expectations for the data provider
        $this->linkRenderer->render(new String_(), Argument::any())->willReturn('string');
        $this->linkRenderer
            ->render(new Compound([new Boolean(), new String_()]), Argument::any())
            ->willReturn(['bool', 'string']);

        $this->adapter = new IterableAdapter(
            $this->linkRenderer->reveal(),
        );
    }

    /** @covers ::supports */
    public function testItSupportsIterableTypes(): void
    {
        self::assertTrue($this->adapter->supports([]));
        self::assertTrue($this->adapter->supports(new ArrayObject([])));
        self::assertFalse($this->adapter->supports(new String_()));
    }

    /** @covers ::render */
    public function testRenderOnlyAcceptsIterableElements(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter->render(new String_(), LinkRenderer::PRESENTATION_NORMAL);
    }

    /**
     * @covers ::render
     * @dataProvider renderingVariations
     */
    public function testRenderProducesExpectedOutputBasedOn(iterable $list, iterable $expected): void
    {
        self::assertSame($expected, $this->adapter->render($list, LinkRenderer::PRESENTATION_NORMAL));
    }

    /** @return array<string, list<iterable>> */
    public function renderingVariations(): array
    {
        return [
            'can handle empty arrays' => [[], []],
            'array with types' => [
                [new String_(), new String_()],
                ['string', 'string'],
            ],
            'Flattens results that produce arrays themselves' => [
                [new String_(), new Compound([new Boolean(), new String_()])],
                ['string', 'bool', 'string'],
            ],
        ];
    }
}
