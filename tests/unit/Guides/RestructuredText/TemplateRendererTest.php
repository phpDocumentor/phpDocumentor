<?php

namespace phpDocumentor\Guides\RestructuredText;

use phpDocumentor\Guides\TemplateRenderer;
use PHPUnit\Framework\TestCase;
use Twig\Environment;

final class TemplateRendererTest extends TestCase
{
    public function test_it_renders_a_twig_template_using_the_given_twig_environment(): void
    {
        $renderedTemplateWithNewline = "output\n";
        $renderedTemplateWithoutNewline = 'output';

        $environment = $this->prophesize(Environment::class);
        $environment->render('basePath/template', ['param1' => 'value1'])->willReturn($renderedTemplateWithNewline);

        $renderer = new TemplateRenderer($environment->reveal(), 'basePath');

        $result = $renderer->render('template', ['param1' => 'value1']);

        $this->assertSame($renderedTemplateWithoutNewline, $result);
    }

    public function test_the_engine_can_be_accessed_for_adding_extension(): void
    {
        $environment = $this->prophesize(Environment::class)->reveal();

        $renderer = new TemplateRenderer($environment, 'basePath');

        $this->assertSame($environment, $renderer->getTemplateEngine());
    }
}
