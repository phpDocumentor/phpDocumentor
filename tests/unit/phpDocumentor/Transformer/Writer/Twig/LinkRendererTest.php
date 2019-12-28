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

use Mockery as m;
use Mockery\Adapter\Phpunit\MockeryTestCase;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Transformer\Router\Router;
use const DIRECTORY_SEPARATOR;
use function str_replace;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
 * @covers ::<private>
 */
final class LinkRendererTest extends MockeryTestCase
{
    /** @var Router */
    private $router;

    /** @var LinkRenderer */
    private $renderer;

    protected function setUp() : void
    {
        $this->router = m::mock(Router::class);

        $this->renderer = new LinkRenderer($this->router);
    }

    /**
     * @covers ::__construct
     * @covers ::getDestination
     * @covers ::setDestination
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

        $result = $this->renderer->render(new Fqsen('\My\Namespace\Class'), 'url');

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
        $collection = new Collection([new Fqsen('\My\Namespace\Class')]);
        $result = $this->renderer->render($collection, 'url');

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath() : void
    {
        $this->router->shouldReceive('generate')
            ->with(m::on(function (Fqsen $fqsen) {
                $this->assertSame((string) $fqsen, '\Class::$property');

                return true;
            }))
            ->andReturn('@Class::$property');

        $result = $this->renderer->convertToRootPath('@Class::$property');

        $this->assertSame('@Class::$property', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderReferenceToType() : void
    {
        $this->router->shouldReceive('generate')->never();

        $result = $this->renderer->render([new Integer()], 'url');

        $this->assertSame(['int'], $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort() : void
    {
        $this->router->shouldReceive('generate')->andReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render(new Fqsen('\My\Namespace\Class'), 'class:short');

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

    public function provideUrls() : array
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org'],
        ];
    }
}
