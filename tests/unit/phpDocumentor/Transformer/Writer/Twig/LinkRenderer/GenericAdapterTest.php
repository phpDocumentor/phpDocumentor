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
use phpDocumentor\Reflection\PseudoTypes\Generic;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

#[CoversClass(GenericAdapter::class)]
final class GenericAdapterTest extends TestCase
{
    use ProphecyTrait;

    /** @var LinkRendererInterface|ObjectProphecy */
    private ObjectProphecy $linkRenderer;

    private GenericAdapter $adapter;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRendererInterface::class);
        $this->adapter = new GenericAdapter($this->linkRenderer->reveal());
    }

    public function testItSupportsGenericTypes(): void
    {
        self::assertTrue($this->adapter->supports(new Generic(null, [])));
        self::assertFalse($this->adapter->supports(new String_()));
    }

    public function testRenderOnlyAcceptsGenericTypes(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->adapter->render(new String_(), LinkRenderer::PRESENTATION_NORMAL);
    }

    public function testRenderLinksTheGenericNameAndItsTypes(): void
    {
        $fqsen = new Fqsen('\\Collection');
        $this->linkRenderer->render($fqsen, LinkRenderer::PRESENTATION_NORMAL)->willReturn('Collection link');
        $this->linkRenderer->render(new String_(), LinkRenderer::PRESENTATION_NORMAL)->willReturn('string');
        $this->linkRenderer->render(new Integer(), LinkRenderer::PRESENTATION_NORMAL)->willReturn('int');

        $generic = new Generic($fqsen, [new String_(), new Integer()]);

        self::assertSame(
            'Collection link&lt;string, int&gt;',
            $this->adapter->render($generic, LinkRenderer::PRESENTATION_NORMAL),
        );
    }

    public function testRenderUsesObjectWhenTheGenericNameCannotBeResolved(): void
    {
        $this->linkRenderer->render(new String_(), LinkRenderer::PRESENTATION_NORMAL)->willReturn('string');

        self::assertSame(
            'object&lt;string&gt;',
            $this->adapter->render(new Generic(null, [new String_()]), LinkRenderer::PRESENTATION_NORMAL),
        );
    }
}
