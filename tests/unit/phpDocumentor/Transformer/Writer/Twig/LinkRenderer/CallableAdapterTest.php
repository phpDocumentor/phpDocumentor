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

use phpDocumentor\Reflection\Types\Boolean;
use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\CallableParameter;
use phpDocumentor\Reflection\Types\Compound;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Intersection;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer\CallableAdapter */
class CallableAdapterTest extends TestCase
{
    use ProphecyTrait;

    private CallableAdapter $adapter;
    private ObjectProphecy|LinkRendererInterface $linkRenderer;

    protected function setUp(): void
    {
        $this->linkRenderer = $this->prophesize(LinkRendererInterface::class);
        $this->adapter = new CallableAdapter($this->linkRenderer->reveal());
    }

    public function testItSupportsCallables(): void
    {
        self::assertTrue($this->adapter->supports(new Callable_()));
        self::assertFalse($this->adapter->supports(new String_()));
    }

    /** @dataProvider callableProvider */
    public function testRenderProducesExpectedOutput(Callable_ $input, string $output): void
    {
        $this->linkRenderer->render(new String_(), LinkRenderer::PRESENTATION_NORMAL)->willReturn('string');
        self::assertSame($output, $this->adapter->render($input, LinkRenderer::PRESENTATION_NORMAL));
    }

    /** @return iterable<string, array{input: Callable_, output: string}> */
    public static function callableProvider(): iterable
    {
        yield 'empty callable' => [
            'input' => new Callable_(),
            'output' => 'callable',
        ];

        yield 'callable with parameters' => [
            'input' => new Callable_(
                [new CallableParameter(new String_()), new CallableParameter(new String_())],
            ),
            'output' => 'callable(string, string)',
        ];

        yield 'callable with return type' => [
            'input' => new Callable_(
                [],
                new String_(),
            ),
            'output' => 'callable(): string',
        ];

        yield 'callable with named parameters' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo'), new CallableParameter(new String_(), 'bar')],
            ),
            'output' => 'callable(string $foo, string $bar)',
        ];

        yield 'callable with variadic parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), null, false, true)],
            ),
            'output' => 'callable(string...)',
        ];

        yield 'callable with named variadic parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo', false, true)],
            ),
            'output' => 'callable(string ...$foo)',
        ];

        yield 'callable with reference parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), null, true)],
            ),
            'output' => 'callable(string&)',
        ];

        yield 'callable with named variadic reference parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo', true, true)],
            ),
            'output' => 'callable(string ...&$foo)',
        ];
    }

    /**
     * @link https://github.com/phpDocumentor/phpDocumentor/issues/3995
     *
     * @dataProvider unionProvider
     */
    public function testRenderJoinsUnionTypes(Callable_ $input, string $output): void
    {
        $union = new Compound([new Integer(), new String_()]);
        $this->linkRenderer->render($union, LinkRenderer::PRESENTATION_NORMAL)
            ->willReturn(['int', 'string']);
        $this->linkRenderer->render(new String_(), LinkRenderer::PRESENTATION_NORMAL)
            ->willReturn('string');

        self::assertSame($output, $this->adapter->render($input, LinkRenderer::PRESENTATION_NORMAL));
    }

    /** @return iterable<string, array{input: Callable_, output: string}> */
    public static function unionProvider(): iterable
    {
        $union = new Compound([new Integer(), new String_()]);

        yield 'union parameter' => [
            'input' => new Callable_([new CallableParameter($union)]),
            'output' => 'callable(int|string)',
        ];

        yield 'union return type' => [
            'input' => new Callable_([], $union),
            'output' => 'callable(): int|string',
        ];

        yield 'named union parameter' => [
            'input' => new Callable_([new CallableParameter($union, 'foo')]),
            'output' => 'callable(int|string $foo)',
        ];

        yield 'variadic union parameter' => [
            'input' => new Callable_([new CallableParameter($union, 'foo', false, true)]),
            'output' => 'callable(int|string ...$foo)',
        ];

        yield 'union parameter mixed with scalar' => [
            'input' => new Callable_([new CallableParameter($union), new CallableParameter(new String_())]),
            'output' => 'callable(int|string, string)',
        ];
    }

    /** @link https://github.com/phpDocumentor/phpDocumentor/issues/3995 */
    public function testRenderJoinsIntersectionTypesWithAmpersand(): void
    {
        $intersection = new Intersection([new String_(), new Boolean()]);
        $this->linkRenderer->render($intersection, LinkRenderer::PRESENTATION_NORMAL)
            ->willReturn(['string', 'bool']);

        self::assertSame(
            'callable(string&bool)',
            $this->adapter->render(
                new Callable_([new CallableParameter($intersection)]),
                LinkRenderer::PRESENTATION_NORMAL,
            ),
        );
    }
}
