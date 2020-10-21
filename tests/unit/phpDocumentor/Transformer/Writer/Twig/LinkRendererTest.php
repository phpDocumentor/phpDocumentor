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

use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
 * @covers ::__construct
 * @covers ::<private>
 */
final class LinkRendererTest extends TestCase
{
    /** @var Router|ObjectProphecy */
    private $router;

    /** @var LinkRenderer */
    private $renderer;

    /** @var ProjectDescriptor */
    private $projectDescriptor;

    protected function setUp() : void
    {
        $this->router = $this->prophesize(Router::class);
        $this->projectDescriptor = new ProjectDescriptor('project');
        $this->projectDescriptor->getIndexes()->set('elements', new Collection());
        $this->renderer = (new LinkRenderer($this->router->reveal()))->withProject($this->projectDescriptor);
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
     * @dataProvider descriptorLinkProvider
     * @covers ::render
     */
    public function testRenderLinkFromDescriptor(DescriptorAbstract $input, string $presentation, string $output) : void
    {
        $this->projectDescriptor->getIndexes()->get('elements')
            ->set((string) $input->getFullyQualifiedStructuralElementName(), $input);

        $this->router->generate($input)->willReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($input, $presentation);

        self::assertSame($output, $result);
    }

    /** array<string, mixed[]> */
    public function descriptorLinkProvider() : array
    {
        return [
            'Class with presentation url' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                LinkRenderer::PRESENTATION_URL,
                'classes/My.Namespace.Class.html',
            ],
            'Class with presentation normal' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                LinkRenderer::PRESENTATION_NORMAL,
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            ],
            'Class with presentation class short' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            ],
            'Class with presentation file short' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                LinkRenderer::PRESENTATION_FILE_SHORT,
                '<a href="classes/My.Namespace.Class.html">' .
                '<abbr title="\My\Namespace\Class">\My\Namespace\Class</abbr></a>',
            ],
            'Class with presentation other' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                'other',
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">other</abbr></a>',
            ],
            'Class with presentation empty' => [
                $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
                '',
                '<a href="classes/My.Namespace.Class.html">\My\Namespace\Class</a>',
            ],
        ];
    }

    private function createClassDescriptor(Fqsen $fqsen) : ClassDescriptor
    {
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);

        return $descriptor;
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

        $this->router->generate(Argument::any())->willReturn('/classes/My.Namespace.Class.html');

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

        $this->router->generate(Argument::any())->willReturn('/classes/My.Namespace.Class.html');

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

        $this->router->generate(Argument::any())->willReturn('/classes/My.Namespace.Class.html');

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
        $this->router->generate(Argument::that(function (Fqsen $fqsen) {
            $this->assertSame((string) $fqsen, '\Class::$property');

            return true;
        }))->willReturn('@Class::$property');

        $result = $this->renderer->convertToRootPath('@Class::$property');

        $this->assertSame('@Class::$property', $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderReferenceToType() : void
    {
        $this->router->generate(Argument::any())->shouldNotBeCalled();

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

        $this->router->generate(Argument::any())->shouldBeCalled()->willReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($fqsen, LinkRenderer::PRESENTATION_CLASS_SHORT);

        $this->assertSame(
            '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            $result
        );
    }

    public function testRenderCollectionLikeReturnType() : void
    {
        $return = new \phpDocumentor\Reflection\Types\Collection(
            new Fqsen('\My\Namespace\Collection'),
            new Object_(new Fqsen('\My\Namespace\Class'))
        );

        $result = $this->renderer->render($return, LinkRenderer::PRESENTATION_CLASS_SHORT);

        $this->assertSame(
            '<abbr title="\My\Namespace\Collection">Collection</abbr>&lt;string|int, ' .
            '<abbr title="\My\Namespace\Class">Class</abbr>&gt;',
            $result
        );
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl(string $url) : void
    {
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
