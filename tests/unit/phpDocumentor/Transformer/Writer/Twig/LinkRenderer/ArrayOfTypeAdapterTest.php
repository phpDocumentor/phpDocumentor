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
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use PHPUnit\Framework\TestCase;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer\ArrayOfTypeAdapter
 * @covers ::<private>
 */
final class ArrayOfTypeAdapterTest extends TestCase
{
    private ArrayOfTypeAdapter $adapter;

    protected function setUp(): void
    {
        $this->adapter = new ArrayOfTypeAdapter();
    }

    /** @covers ::supports */
    public function testItSupportsArraysOfType(): void
    {
        self::assertTrue($this->adapter->supports([new String_()]));
        self::assertFalse($this->adapter->supports(new Fqsen('\MyAwesome\Object')));
    }

    /** @covers ::render */
    public function testRenderOnlyAcceptsArraysOfType(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter->render(
            new Fqsen('\MyAwesome\Object'),
            LinkRenderer::PRESENTATION_NORMAL,
        );
    }

    /**
     * @covers ::render
     * @dataProvider renderingVariations
     */
    public function testRenderProducesExpectedOutputBasedOn(array $value, iterable $expected): void
    {
        self::assertSame($expected, $this->adapter->render($value, LinkRenderer::PRESENTATION_NORMAL));
    }

    /** @return array<string, list<iterable>> */
    public function renderingVariations(): array
    {
        return [
            'Converts arrays of types to their string version' => [
                [new String_(), new Integer()],
                ['string', 'int'],
            ],
        ];
    }
}
