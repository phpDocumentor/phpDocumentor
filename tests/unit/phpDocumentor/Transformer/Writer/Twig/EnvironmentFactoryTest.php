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
use phpDocumentor\Guides\Graphs\Twig\UmlExtension;
use phpDocumentor\Guides\NodeRenderers\NodeRenderer;
use phpDocumentor\Guides\ReferenceResolvers\DocumentNameResolverInterface;
use phpDocumentor\Guides\Renderer\UrlGenerator\UrlGeneratorInterface;
use phpDocumentor\Guides\Twig\AssetsExtension;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\HtmlFormatter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\Test\TestLogger;
use Twig\Environment;
use Twig\Extension\DebugExtension;
use Twig\Loader\ChainLoader;
use Twig\Loader\FilesystemLoader;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\EnvironmentFactory */
final class EnvironmentFactoryTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    /** @var Router */
    private $router;

    private EnvironmentFactory $factory;

    protected function setUp(): void
    {
        $this->router = $this->prophesize(Router::class);
        $markDownConverter = $this->prophesize(ConverterInterface::class);

        $relativePathToRootConverter = new RelativePathToRootConverter($this->router->reveal());
        $this->factory = new EnvironmentFactory(
            new LinkRenderer($this->router->reveal(), new HtmlFormatter()),
            $markDownConverter->reveal(),
            $relativePathToRootConverter,
            new PathBuilder($this->router->reveal(), $relativePathToRootConverter),
            [
                new AssetsExtension(
                    new TestLogger(),
                    $this->prophesize(NodeRenderer::class)->reveal(),
                    $this->prophesize(DocumentNameResolverInterface::class)->reveal(),
                    $this->prophesize(UrlGeneratorInterface::class)->reveal(),
                ),
                new UmlExtension([], 'plantuml'),
            ],
            ['./data/templates'],
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     */
    public function testItCreatesATwigEnvironmentWithThephpDocumentorExtension(): void
    {
        $template = self::faker()->template();

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $projectDescriptor = self::faker()->projectDescriptor(
            [self::faker()->versionDescriptor([$apiSetDescriptor])],
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(8, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     */
    public function testItCreatesATwigEnvironmentWithTheCorrectTemplateLoaders(): void
    {
        $template = self::faker()->template();
        $mountManager = $template->files();

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $projectDescriptor = self::faker()->projectDescriptor(
            [self::faker()->versionDescriptor([$apiSetDescriptor])],
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        /** @var ChainLoader $loader */
        $loader = $environment->getLoader();

        $this->assertInstanceOf(ChainLoader::class, $loader);
        $this->assertEquals(
            [
                new FlySystemLoader($mountManager->getFilesystem('templates'), '', 'base'),
                new FlySystemLoader($mountManager->getFilesystem('template'), 'guides', 'base'),
                new FlySystemLoader($mountManager->getFilesystem('template')),
                new FilesystemLoader('./data/templates'),
            ],
            $loader->getLoaders(),
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     * @uses \phpDocumentor\Transformer\Template\Parameter
     */
    public function testTheCreatedEnvironmentHasTheDebugExtension(): void
    {
        $template = self::faker()->template();

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $projectDescriptor = self::faker()->projectDescriptor(
            [self::faker()->versionDescriptor([$apiSetDescriptor])],
        );
        $environment = $this->factory->create($projectDescriptor, $apiSetDescriptor, $template);

        $this->assertFalse($environment->getCache());
        $this->assertTrue($environment->isDebug());
        $this->assertTrue($environment->isAutoReload());
        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(8, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
        $this->assertTrue($environment->hasExtension(DebugExtension::class));
    }
}
