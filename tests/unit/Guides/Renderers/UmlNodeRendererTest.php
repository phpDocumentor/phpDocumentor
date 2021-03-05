<?php

declare(strict_types=1);

namespace phpDocumentor\Guides\Renderers;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Guides\Environment;
use phpDocumentor\Guides\Nodes\UmlNode;
use phpDocumentor\Guides\Renderer;
use phpDocumentor\Transformer\Writer\Graph\PlantumlRenderer;

final class UmlNodeRendererTest extends MockeryTestCase
{
    public function test_it_can_render_a_uml_diagram() : void
    {
        $plantumlRenderer = m::mock(PlantumlRenderer::class);
        $environment = m::mock(Environment::class);
        $twig = m::mock(Renderer::class);

        $umlNode = new UmlNode('value');
        $umlNode->setEnvironment($environment);
        $renderedSvg = '<svg></svg>';

        $plantumlRenderer->shouldReceive('render')->andReturn($renderedSvg);
        $twig
            ->shouldReceive('render')
            ->with(
                'uml.html.twig',
                [
                    'umlNode' => $umlNode,
                    'svg' => $renderedSvg,
                ]
            )
            ->andReturn('template');

        $renderer = new UmlNodeRenderer($umlNode, $plantumlRenderer, $twig);
        $result = $renderer->render($umlNode);

        self::assertSame('template', $result);
    }
}
