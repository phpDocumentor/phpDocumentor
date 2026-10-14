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
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Metadata\Metadata;
use phpDocumentor\Transformer\Router\Router;
use phpDocumentor\Transformer\Writer\Twig\LinkRenderer\HtmlFormatter;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Psr\Log\NullLogger;
use Twig\Environment;

use function file_put_contents;
use function mkdir;
use function rmdir;
use function sys_get_temp_dir;
use function trim;
use function uniqid;
use function unlink;

/** Renders the metadata component of the default template. */
final class MetadataComponentTest extends TestCase
{
    use ProphecyTrait;
    use Faker;

    private string $directory;

    protected function setUp(): void
    {
        $this->directory = sys_get_temp_dir() . '/phpdoc-metadata-component-' . uniqid();
        mkdir($this->directory . '/metadata', 0777, true);
        file_put_contents($this->directory . '/metadata/my_key.html.twig', '<b>{{ metadata.key }}</b>');
    }

    protected function tearDown(): void
    {
        unlink($this->directory . '/metadata/my_key.html.twig');
        rmdir($this->directory . '/metadata');
        rmdir($this->directory);
    }

    // Scenario: No metadata section is shown when nothing was rendered
    public function testNoMetadataSectionIsShownWhenNothingWasRendered(): void
    {
        $node = self::faker()->classDescriptor(new Fqsen('\\Foo'));
        $node->setMetadata([$this->metadata('unknown_key')]);

        $output = $this->render($node);

        $this->assertSame('', trim($output));
    }

    // Scenario: No metadata section is shown when nothing was rendered
    public function testNoMetadataSectionIsShownForADescriptorWithoutMetadata(): void
    {
        $node = self::faker()->classDescriptor(new Fqsen('\\Foo'));

        $this->assertSame('', trim($this->render($node)));
    }

    // Scenario: Metadata is rendered with the template of the extension that declares its key
    public function testTheMetadataSectionContainsTheRenderedMetadata(): void
    {
        $node = self::faker()->classDescriptor(new Fqsen('\\Foo'));
        $node->setMetadata([$this->metadata('my_key')]);

        $output = $this->render($node);

        $this->assertStringContainsString('phpdocumentor-metadata', $output);
        $this->assertStringContainsString('<b>my_key</b>', $output);
    }

    private function render(object $node): string
    {
        $router = $this->prophesize(Router::class);
        $relativePathToRootConverter = new RelativePathToRootConverter($router->reveal());
        $factory = new EnvironmentFactory(
            new LinkRenderer($router->reveal(), new HtmlFormatter()),
            $this->prophesize(ConverterInterface::class)->reveal(),
            $relativePathToRootConverter,
            new PathBuilder($router->reveal(), $relativePathToRootConverter),
            [
                new AssetsExtension(
                    new NullLogger(),
                    $this->prophesize(NodeRenderer::class)->reveal(),
                    $this->prophesize(DocumentNameResolverInterface::class)->reveal(),
                    $this->prophesize(UrlGeneratorInterface::class)->reveal(),
                ),
                new UmlExtension([], 'plantuml'),
            ],
            ['./data/templates'],
            './data/templates',
            ['my-extension' => ['path' => $this->directory, 'keys' => ['my_key']]],
        );

        $apiSetDescriptor = self::faker()->apiSetDescriptor();
        $projectDescriptor = self::faker()->projectDescriptor(
            [self::faker()->versionDescriptor([$apiSetDescriptor])],
        );

        /** @var Environment $environment */
        $environment = $factory->create($projectDescriptor, $apiSetDescriptor, self::faker()->template());

        return $environment->render('default/components/metadata.html.twig', ['node' => $node]);
    }

    private function metadata(string $key): Metadata
    {
        return new class ($key) implements Metadata {
            public function __construct(private readonly string $key)
            {
            }

            public function key(): string
            {
                return $this->key;
            }
        };
    }
}
