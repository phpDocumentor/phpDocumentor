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

use League\CommonMark\MarkdownConverterInterface;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Guides\Metas;
use phpDocumentor\Guides\Twig\TocExtension;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
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

    protected function setUp() : void
    {
        $this->router = $this->prophesize(Router::class);
        $markDownConverter = $this->prophesize(MarkdownConverterInterface::class);

        $this->factory = new EnvironmentFactory(
            new LinkRenderer($this->router->reveal()),
            new TocExtension(new Metas()),
            $markDownConverter->reveal()
        );
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     *
     * @covers ::create
     */
    public function testItCreatesATwigEnvironmentWithThephpDocumentorExtension() : void
    {
        $transformation = $this->faker()->transformation();

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(6, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     *
     * @covers ::create
     */
    public function testItCreatesATwigEnvironmentWithTheCorrectTemplateLoaders() : void
    {
        $transformation = $this->faker()->transformation();

        $mountManager = $transformation->template()->files();

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        /** @var ChainLoader $loader */
        $loader = $environment->getLoader();

        $this->assertInstanceOf(ChainLoader::class, $loader);
        $this->assertEquals(
            [
                new FlySystemLoader($mountManager->getFilesystem('templates'), '', 'base'),
                new FlySystemLoader($mountManager->getFilesystem('template')),
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
    public function testTheCreatedEnvironmentHasTheDebugExtension() : void
    {
        $transformation = $this->faker()->transformation();

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        $this->assertFalse($environment->getCache());
        $this->assertTrue($environment->isDebug());
        $this->assertTrue($environment->isAutoReload());
        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(6, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
        $this->assertTrue($environment->hasExtension(DebugExtension::class));
    }
}
