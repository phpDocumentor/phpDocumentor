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

namespace phpDocumentor\Transformer\Writer\Twig;

use League\CommonMark\ConverterInterface;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Guides\Graphs\Renderer\DiagramRenderer;
use phpDocumentor\Guides\Graphs\Twig\UmlExtension;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Guides\Twig\EnvironmentBuilder;
use phpDocumentor\Guides\Twig\TwigRenderer;
use phpDocumentor\Guides\UrlGenerator;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\HtmlFormatter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\Test\TestLogger;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory
 * @covers ::__construct
 * @covers ::<private>
 */
final class EnvironmentFactoryTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /** @var Router */
    private $router;

    /** @var EnvironmentFactory */
    private $factory;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(Router::class);
        $markDownConverter = $this->prophesize(ConverterInterface::class);

        $relativePathToRootConverter = new RelativePathToRootConverter($this->router->reveal());
        $this->factory = new EnvironmentFactory(
            new LinkRenderer($this->router->reveal(), new HtmlFormatter()),
            $markDownConverter->reveal(),
            new AssetsExtension(
                new TestLogger(),
                new TwigRenderer([], $this->prophesize(EnvironmentBuilder::class)->reveal()),
                new UrlGenerator()
            ),
            new UmlExtension($this->prophesize(DiagramRenderer::class)->reveal()),
            $relativePathToRootConverter,
            new PathBuilder($this->router->reveal(), $relativePathToRootConverter)
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     *
     * @covers ::create
     */
    public function testItCreatesATwigEnvironmentWithThephpDocumentorExtension(): void
    {
        $template = $this->faker()->template();

        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->faker()->projectDescriptor(
            [$this->faker()->versionDescriptor([$apiSetDescriptor])]
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(7, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     *
     * @covers ::create
     */
    public function testItCreatesATwigEnvironmentWithTheCorrectTemplateLoaders(): void
    {
        $template = $this->faker()->template();
        $mountManager = $template->files();

        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->faker()->projectDescriptor(
            [$this->faker()->versionDescriptor([$apiSetDescriptor])]
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        /** @var ChainLoader $loader */
        $loader = $environment->getLoader();

        $this->assertInstanceOf(ChainLoader::class, $loader);
        $this->assertEquals(
            [
                new FlySystemLoader($mountManager->getFilesystem('templates'), '', 'base'),
                new FlySystemLoader($mountManager->getFilesystem('template')),
                new FlySystemLoader($mountManager->getFilesystem('guides'), '', 'guides'),
            ],
            $loader->getLoaders()
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     * @uses \phpDocumentor\Transformer\Template\Parameter
     *
     * @covers ::create
     */
    public function testTheCreatedEnvironmentHasTheDebugExtension(): void
    {
        $template = $this->faker()->template();

        $apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $projectDescriptor = $this->faker()->projectDescriptor(
            [$this->faker()->versionDescriptor([$apiSetDescriptor])]
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        $this->assertFalse($environment->getCache());
        $this->assertTrue($environment->isDebug());
        $this->assertTrue($environment->isAutoReload());
        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(7, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
        $this->assertTrue($environment->hasExtension(DebugExtension::class));
    }
}
