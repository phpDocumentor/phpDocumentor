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

use Generator;
use phpDocumentor\Descriptor\ApiSetDescriptor;
use phpDocumentor\Descriptor\ClassDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Faker\Faker;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Type;
use phpDocumentor\Reflection\Types\Array_;
use phpDocumentor\Reflection\Types\Integer;
use phpDocumentor\Reflection\Types\Iterable_;
use phpDocumentor\Reflection\Types\Mixed_;
use phpDocumentor\Reflection\Types\Nullable;
use phpDocumentor\Reflection\Types\Object_;
use phpDocumentor\Reflection\Types\String_;
use phpDocumentor\Transformer\Router\Router;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

use function get_class;
use function gettype;
use function is_object;

/**
 * @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\LinkRenderer
 * @covers ::__construct
 * @covers ::<private>
 */
final class LinkRendererTest extends TestCase
{
    use Faker;
    use ProphecyTrait;

    /** @var Router|ObjectProphecy */
    private $router;

    /** @var LinkRenderer */
    private $renderer;

    /** @var ApiSetDescriptor */
    private $apiSetDescriptor;

    /** @var ProjectDescriptor */
    private $projectDescriptor;

    protected function setUp(): void
    {
        $this->router           = $this->prophesize(Router::class);
        $this->projectDescriptor = new ProjectDescriptor('project');
        $this->apiSetDescriptor = $this->faker()->apiSetDescriptor();
        $this->apiSetDescriptor->getIndexes()->set('elements', new Collection());
        $this->renderer = (new LinkRenderer($this->router->reveal()))
            ->withProject($this->projectDescriptor)
            ->withDocumentationSet($this->apiSetDescriptor);
    }

    /**
     * @covers ::getDestination
     * @covers ::setDestination
     */
    public function testGetAndSetDestination(): void
    {
        $this->renderer->setDestination('destination');

        $this->assertSame('destination', $this->renderer->getDestination());
    }

    /**
     * @param ClassDescriptor|Fqsen $input
     *
     * @dataProvider descriptorLinkProvider
     * @covers ::render
     */
    public function testRenderLinkFromDescriptor($input, string $presentation, string $output): void
    {
        $classDescriptor = $this->createClassDescriptor(new Fqsen('\My\Namespace\Class'));
        $this->apiSetDescriptor->getIndexes()->get('elements')
            ->set((string) $classDescriptor->getFullyQualifiedStructuralElementName(), $classDescriptor);

        $this->router->generate($classDescriptor)->willReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($input, $presentation);

        self::assertSame($output, $result);
    }

    /** @return Generator<string, mixed[]> */
    public function descriptorLinkProvider(): Generator
    {
        $inputs = [
            $this->createClassDescriptor(new Fqsen('\My\Namespace\Class')),
            new Fqsen('\My\Namespace\Class'),
            new \phpDocumentor\Reflection\DocBlock\Tags\Reference\Fqsen(new Fqsen('\My\Namespace\Class')),
            '\My\Namespace\Class',
        ];

        foreach ($inputs as $input) {
            yield from $this->baseLinkProvider($input);
        }
    }

    public function baseLinkProvider($input): array
    {
        $name = is_object($input) ? get_class($input) : gettype($input);

        return [
            $name . ' with presentation url' => [
                $input,
                LinkRenderer::PRESENTATION_URL,
                'classes/My.Namespace.Class.html',
            ],
            $name . ' with presentation normal' => [
                $input,
                LinkRenderer::PRESENTATION_NORMAL,
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            ],
            $name . ' with presentation class short' => [
                $input,
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            ],
            $name . ' with presentation file short' => [
                $input,
                LinkRenderer::PRESENTATION_FILE_SHORT,
                '<a href="classes/My.Namespace.Class.html">' .
                '<abbr title="\My\Namespace\Class">\My\Namespace\Class</abbr></a>',
            ],
            $name . ' with presentation other' => [
                $input,
                'other',
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">other</abbr></a>',
            ],
            $name . ' with presentation empty' => [
                $input,
                '',
                '<a href="classes/My.Namespace.Class.html">\My\Namespace\Class</a>',
            ],
        ];
    }

    private function createClassDescriptor(Fqsen $fqsen): ClassDescriptor
    {
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);

        return $descriptor;
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithNullableFqsen(): void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->apiSetDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router->generate(Argument::any())->willReturn('/classes/My.Namespace.Class.html');

        $nullable = new Nullable(new Object_($fqsen));
        $result = $this->renderer->render($nullable, LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['classes/My.Namespace.Class.html', 'null'], $result);
    }

    /**
     * @covers ::render
     * @covers ::convertToRootPath
     */
    public function testRenderWithCollectionOfFqsensAndRepresentationUrl(): void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->apiSetDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router->generate(Argument::any())->willReturn('/classes/My.Namespace.Class.html');

        $this->renderer->setDestination('/root/of/project');
        $collection = new Collection([$fqsen]);
        $result = $this->renderer->render($collection, LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['../../../classes/My.Namespace.Class.html'], $result);
    }

    /**
     * @covers ::convertToRootPath
     */
    public function testConvertToRootPathWithUrlAndAtSignInRelativePath(): void
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
    public function testRenderReferenceToType(): void
    {
        $this->router->generate(Argument::any())->shouldNotBeCalled();

        $result = $this->renderer->render([new Integer()], LinkRenderer::PRESENTATION_URL);

        $this->assertSame(['int'], $result);
    }

    /**
     * @covers ::render
     */
    public function testRenderWithFqsenAndRepresentationClassShort(): void
    {
        $fqsen = new Fqsen('\My\Namespace\Class');
        $descriptor = new ClassDescriptor();
        $descriptor->setFullyQualifiedStructuralElementName($fqsen);
        $this->apiSetDescriptor->getIndexes()->get('elements')->set('\My\Namespace\Class', $descriptor);

        $this->router->generate(Argument::any())->shouldBeCalled()->willReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($fqsen, LinkRenderer::PRESENTATION_CLASS_SHORT);

        $this->assertSame(
            '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>',
            $result
        );
    }

    /**
     * @dataProvider typeRouteProvider
     */
    public function testRenderType(Type $input, string $presentation, string $output): void
    {
        $classDescriptor = $this->createClassDescriptor(new Fqsen('\My\Namespace\Class'));
        $this->apiSetDescriptor->getIndexes()->get('elements')
            ->set((string) $classDescriptor->getFullyQualifiedStructuralElementName(), $classDescriptor);

        $this->router->generate($classDescriptor)->willReturn('/classes/My.Namespace.Class.html');

        $result = $this->renderer->render($input, $presentation);

        self::assertSame($output, $result);
    }

    public function typeRouteProvider(): array
    {
        return [
            'collection with class' => [
                new \phpDocumentor\Reflection\Types\Collection(
                    new Fqsen('\My\Namespace\Collection'),
                    new Object_(new Fqsen('\My\Namespace\Class'))
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                '<abbr title="\My\Namespace\Collection">Collection</abbr>&lt;string|int, ' .
                '<a href="classes/My.Namespace.Class.html"><abbr title="\My\Namespace\Class">Class</abbr></a>&gt;',
            ],
            'collection with not existing class' => [
                new \phpDocumentor\Reflection\Types\Collection(
                    new Fqsen('\My\Namespace\Collection'),
                    new Object_(new Fqsen('\My\Namespace\OtherClass'))
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                '<abbr title="\My\Namespace\Collection">Collection</abbr>&lt;string|int, ' .
                '<abbr title="\My\Namespace\OtherClass">OtherClass</abbr>&gt;',
            ],
            'array with scalar only' => [
                new Array_(
                    new String_(),
                    new Array_(new String_(), new Mixed_())
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                'array&lt;array&lt;mixed, string&gt;, string&gt;',
            ],
            'array with class link' => [
                new Array_(
                    new Object_(new Fqsen('\My\Namespace\Class')),
                    new String_()
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                'array&lt;string, <a href="classes/My.Namespace.Class.html">' .
                '<abbr title="\My\Namespace\Class">Class</abbr></a>&gt;',
            ],
            'array without key' => [
                new Array_(
                    new String_()
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                'array&lt;string|int, string&gt;',
            ],
            'iteratable with scalar only' => [
                new Iterable_(
                    new String_(),
                    new Iterable_(new String_(), new Mixed_())
                ),
                LinkRenderer::PRESENTATION_CLASS_SHORT,
                'iteratable&lt;iteratable&lt;mixed, string&gt;, string&gt;',
            ],
        ];
    }

    /**
     * @covers ::render
     * @dataProvider provideUrls
     */
    public function testRenderWithUrl(string $url): void
    {
        $result = $this->renderer->render($url, LinkRenderer::PRESENTATION_URL);

        $this->assertSame($url, $result);
    }

    public function provideUrls(): array
    {
        return [
            ['http://phpdoc.org'],
            ['https://phpdoc.org'],
            ['ftp://phpdoc.org'],
        ];
    }
}
