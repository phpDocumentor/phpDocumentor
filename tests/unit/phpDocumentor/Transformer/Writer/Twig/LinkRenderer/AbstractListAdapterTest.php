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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\PseudoTypes\List_;
use phpDocumentor\Reflection\Types\AbstractList;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Collection;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer\AbstractListAdapter
 * @covers ::__construct
 */
final class AbstractListAdapterTest extends TestCase
{
    use ProphecyTrait;

    /** @var LinkRendererInterface|ObjectProphecy  */
    private ObjectProphecy $linkRenderer;
    private AbstractListAdapter $adapter;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRendererInterface::class);

        // pre-loaded expectations for the data provider
        $this->linkRenderer->render(new String_(), Argument::any())->willReturn('string');
        $this->linkRenderer->render(new Integer(), Argument::any())->willReturn('int');
        $this->linkRenderer->render(new Mixed_(), Argument::any())->willReturn('mixed');
        $this->linkRenderer
            ->render(new Compound([new String_(), new Integer()]), Argument::any())
            ->willReturn('string|int');
        $this->linkRenderer
            ->render(new Fqsen('\MyAwesome\Collection'), Argument::any())
            ->willReturn('\MyAwesome\Collection');
        $this->linkRenderer
            ->render(new Compound([new Boolean(), new String_()]), Argument::any())
            ->willReturn(['bool', 'string']);

        $this->adapter = new AbstractListAdapter(
            $this->linkRenderer->reveal(),
        );
    }

    /** @covers ::supports */
    public function testItSupportsAbstractLists(): void
    {
        self::assertTrue($this->adapter->supports(new Array_()));
        self::assertFalse($this->adapter->supports(new String_()));
    }

    /** @covers ::render */
    public function testRenderOnlyAcceptsAbstractLists(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter->render(new String_(), LinkRenderer::PRESENTATION_NORMAL);
    }

    /**
     * @covers ::render
     * @dataProvider renderingVariations
     */
    public function testRenderProducesExpectedOutputBasedOn(AbstractList $list, string $expected): void
    {
        self::assertSame($expected, $this->adapter->render($list, LinkRenderer::PRESENTATION_NORMAL));
    }

    /** @return array<string, list<AbstractList|string>> */
    public static function renderingVariations(): array
    {
        return [
            'Array with undefined key nor value' => [
                new Array_(),
                'array&lt;string|int, mixed&gt;',
            ],
            'Array with undefined key and string value' => [
                new Array_(new String_()),
                'array&lt;string|int, string&gt;',
            ],
            'Array with integer key, and a value that is rendered as an array of boolean and string' => [
                new Array_(new Compound([new Boolean(), new String_()]), new Integer()),
                'array&lt;int, bool|string&gt;',
            ],
            'Array with integer value, and a key that is rendered as an array of boolean and string' => [
                new Array_(new Integer(), new Compound([new Boolean(), new String_()])),
                'array&lt;bool|string, int&gt;',
            ],
            'List with undefined key nor value' => [
                new List_(),
                'array&lt;int, mixed&gt;',
            ],
            'Collection with string value' => [
                new Collection(new Fqsen('\MyAwesome\Collection'), new String_()),
                '\MyAwesome\Collection&lt;string|int, string&gt;',
            ],
            'Collection of unknown type' => [
                new Collection(null, new String_()),
                'object&lt;string|int, string&gt;',
            ],
            'Unknown AbstractList with string key and int value' => [
                new class(new Integer(), new String_()) extends AbstractList {},
                'mixed&lt;string, int&gt;',
            ],
        ];
    }
}
