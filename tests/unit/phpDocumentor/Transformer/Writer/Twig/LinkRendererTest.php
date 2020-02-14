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
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Transformer\Router\Router;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
 * @covers ::__construct
 * @covers ::<private>
 */
final class LinkRendererTest extends MockeryTestCase
{
    /** @var Router */
    private $router;

    /** @var LinkRenderer */
    private $renderer;

    /** @var ProjectDescriptor */
    private $projectDescriptor;

    protected function setUp() : void
    {
        $this->router = m::mock(Router::class);
        $this->projectDescriptor = new ProjectDescriptor('project');
        $this->projectDescriptor->getIndexes()->set('elements', new Collection());
        $this->renderer = (new LinkRenderer($this->router))->withProject($this->projectDescriptor);
    }

    /**
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
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->projectDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router
            ->shouldReceive('generate')
            ->andReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($fqsen, LinkRenderer::PRESENTATION_URL);

        $this->assertSame('classes/My.Namespace.Class.html', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithNullableFqsen() : void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->projectDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router
            ->shouldReceive('generate')
            ->andReturn('/classes/My.Namespace.Class.html');

        $nullable = new Nullable(new Object_($fqsen));
        $result = $this->renderer->render($nullable, LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['classes/My.Namespace.Class.html', 'null'], $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionOfFqsensAndRepresentationUrl() : void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->projectDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router
            ->shouldReceive('generate')
            ->andReturn('/classes/My.Namespace.Class.html');

        $this->renderer->setDestination('/root/of/project');
        $collection = new Collection([$fqsen]);
        $result = $this->renderer->render($collection, LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath() : void
    {
        $this->router->shouldReceive('generate')
            ->with(
                m::on(
                    function (Fqsen $fqsen) {
                        $this->assertSame((string) $fqsen, '\Class::$property');

                        return true;
                    }
                )
            )
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

        $result = $this->renderer->render([new Integer()], LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['int'], $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort() : void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->projectDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router->shouldReceive('generate')->andReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($fqsen, LinkRenderer::PRESENTATION_CLASS_SHORT);

        $this->assertSame(
            '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            $result
        );
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl(string $url) : void
    {
        $this->router->shouldReceive('generate')->andReturn($url);

        $result = $this->renderer->render($url, LinkRenderer::PRESENTATION_URL);

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
