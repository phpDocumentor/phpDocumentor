<?php

declare(strict_types=1);

namespace phpDocumentor\Transformer\Writer\Twig\LinkRenderer;

use phpDocumentor\Reflection\Types\Callable_;
use phpDocumentor\Reflection\Types\CallableParameter;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer;
use phpDocumentor\Transformer\Writer\Twig\LinkRendererInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class CallableAdapterTest extends TestCase
{
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
    public function callableProvider(): iterable
    {
        yield
          'empty callable' => [
              'input' => new Callable_(),
              'output' => 'callable',
          ];

        yield
          'callable with parameters' => [
              'input' => new Callable_(
                  [new CallableParameter(new String_()), new CallableParameter(new String_())]
              ),
              'output' => 'callable(string, string)',
          ];

        yield
          'callable with return type' => [
              'input' => new Callable_(
                  [],
                  new String_()
              ),
              'output' => 'callable(): string',
          ];

        yield 'callable with named parameters' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo'), new CallableParameter(new String_(), 'bar')]
            ),
            'output' => 'callable(string $foo, string $bar)',
        ];

        yield 'callable with variadic parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), null, false, true)]
            ),
            'output' => 'callable(string...)',
        ];

        yield 'callable with named variadic parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo', false, true)]
            ),
            'output' => 'callable(string ...$foo)',
        ];

        yield 'callable with reference parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), null, true)]
            ),
            'output' => 'callable(string&)',
        ];

        yield 'callable with named variadic reference parameter' => [
            'input' => new Callable_(
                [new CallableParameter(new String_(), 'foo', true, true)]
            ),
            'output' => 'callable(string ...&$foo)',
        ];
    }
}
