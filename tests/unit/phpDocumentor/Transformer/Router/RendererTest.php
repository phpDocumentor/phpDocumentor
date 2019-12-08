<?php

declare(strict_types=1);

/**
 * This file is part of phpDocumentor.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @link      http://phpdoc.org
 */

namespace phpDocumentor\Transformer\Router;

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Type\CollectionDescriptor;
use phpDocumentor\Uri;
use const DIRECTORY_SEPARATOR;
use function str_replace;

/**
 * Test class for phpDocumentor\Transformer\Router\Renderer
 *
 * @coversDefaultClass \phpDocumentor\Transformer\Router\Renderer
 * @covers ::<private>
 */
final class RendererTest extends MockeryTestCase
{
    /** @var Router */
    private $router;

    /** @var Renderer */
    private $renderer;

    protected function setUp() : void
    {
        $this->router = m::mock(Router::class);

        $this->renderer = new Renderer($this->router);
    }

    /**
     * @covers \phpDocumentor\Transformer\Router\Renderer::__construct
     * @covers \phpDocumentor\Transformer\Router\Renderer::getDestination
     * @covers \phpDocumentor\Transformer\Router\Renderer::setDestination
     */
    public function testGetAndSetDestination() : void
    {
        $this->renderer->setDestination('destination');

        $this->assertSame('destination', $this->renderer->getDestination());
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithFqsenAndRepresentationUrl() : void
    {
        $this->router
            ->shouldReceive('generate')
            ->andReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render('\My\Namespace\Class', 'url');

        $this->assertSame('classes/My.Namespace.Class.html', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionOfFqsensAndRepresentationUrl() : void
    {
        $this->router
            ->shouldReceive('generate')
            ->andReturn('/classes/My.Namespace.Class.html');

        $this->renderer->setDestination(str_replace('/', DIRECTORY_SEPARATOR, '/root/of/project'));
        $collection = new Collection(['\My\Namespace\Class']);
        $result     = $this->renderer->render($collection, 'url');

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath() : void
    {
        $this->markTestIncomplete('Redo this test');
        $this->router->shouldReceive('generate')
            ->with('@Class::$property')
            ->andReturn('@Class::$property');

        $result = $this->renderer->convertToRootPath('@Class::$property');

        $this->assertSame('@Class::$property', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsNotArrayAndRepresentationUrl() : void
    {
        $this->router->shouldReceive('generate')->andReturn('ClassDescriptor');

        $collectionDescriptor = $this->givenACollectionDescriptor('class');
        $collectionDescriptor->setKeyTypes(['ClassDescriptor']);
        $result = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor&lt;ClassDescriptor,ClassDescriptor&gt;', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionDescriptorWithNameIsArrayAndRepresentationUrl() : void
    {
        $this->router->shouldReceive('generate')->andReturn('ClassDescriptor');

        $collectionDescriptor = $this->givenACollectionDescriptor('array');
        $result               = $this->renderer->render($collectionDescriptor, 'url');

        $this->assertSame('ClassDescriptor[]', $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort() : void
    {
        $this->router->shouldReceive('generate')->andReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render('\My\Namespace\Class', 'class:short');

        $this->assertSame('<a href="classes/My.Namespace.Class.html">Class</a>', $result);
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl(string $url) : void
    {
        $this->router->shouldReceive('generate')->andReturn($url);

        $result = $this->renderer->render($url, 'url');

        $this->assertSame($url, $result);
    }

    private function givenACollectionDescriptor(string $name) : CollectionDescriptor
    {
        $classDescriptor = m::mock('phpDocumentor\Descriptor\ClassDescriptor');
        $classDescriptor->shouldReceive('getName')->andReturn($name);
        $collectionDescriptor = new CollectionDescriptor($classDescriptor);
        $collectionDescriptor->setTypes(['ClassDescriptor']);
        return $collectionDescriptor;
    }

    public function provideUrls() : array
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org'],
        ];
    }
}
