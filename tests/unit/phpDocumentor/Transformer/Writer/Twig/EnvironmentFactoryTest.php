<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Writer\Twig;

use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Parser\Cache\Locator;
use phpDocumentor\Path;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Template\Parameter;
use PHPUnit\Framework\TestCase;
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
    use Faker;

    /** @var Locator */
    private $cacheLocator;

    /** @var Router */
    private $router;

    /** @var EnvironmentFactory */
    private $factory;

    protected function setUp() : void
    {
        $this->router = $this->prophesize(Router::class);
        $this->cacheLocator = $this->prophesize(Locator::class);

        $this->factory = new EnvironmentFactory(
            new LinkRenderer($this->router->reveal()),
            $this->cacheLocator->reveal()
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
        $cacheFolder = new Path('/tmp/cache/twig');

        $this->cacheLocator->locate('twig')->willReturn($cacheFolder);
        $transformation = $this->faker()->transformation();

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(4, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
        $this->assertSame((string) $cacheFolder, $environment->getCache());
    }

    /**
     * @uses \phpDocumentor\Descriptor\ProjectDescriptor
     * @uses \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
     *
     * @covers ::create
     */
    public function testItCreatesATwigEnvironmentWithTheCorrectTemplateLoaders() : void
    {
        $cacheFolder = new Path('/tmp/cache/twig');

        $this->cacheLocator->locate('twig')->willReturn($cacheFolder);

        $transformation = $this->faker()->transformation();
        $mountManager = $transformation->template()->files();

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        /** @var ChainLoader $loader */
        $loader = $environment->getLoader();

        $this->assertInstanceOf(ChainLoader::class, $loader);
        $this->assertEquals(
            [
                new FlySystemLoader($mountManager->getFilesystem('templates')),
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
    public function testTheCreatedEnvironmentHasTheDebugExtensionWhenTheCorrectParameterIsSet() : void
    {
        $cacheFolder = new Path('/tmp/cache/twig');

        $this->cacheLocator->locate('twig')->willReturn($cacheFolder);

        $transformation = $this->faker()->transformation();
        $transformation->setParameters([new Parameter('twig-debug', 'true')]);

        $environment = $this->factory->create(new ProjectDescriptor('name'), $transformation, '/home');

        $this->assertTrue($environment->isDebug());
        $this->assertTrue($environment->isAutoReload());
        $this->assertInstanceOf(Environment::class, $environment);
        $this->assertCount(5, $environment->getExtensions());
        $this->assertTrue($environment->hasExtension(Extension::class));
        $this->assertTrue($environment->hasExtension(DebugExtension::class));
    }
}
