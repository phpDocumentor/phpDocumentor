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

use phpDocumentor\Reflection\Metadata\Metadata;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;
use Twig\Loader\ArrayLoader;
use Twig\Loader\LoaderInterface;

/** @coversDefaultClass \phpDocumentor\Transformer\Writer\Twig\MetadataTemplateResolver */
final class MetadataTemplateResolverTest extends TestCase
{
    private const EXTENSIONS = [
        'my-extension' => ['path' => '/extensions/my-extension/templates', 'keys' => ['my_key']],
        'other-extension' => ['path' => '/extensions/other-extension/templates', 'keys' => ['other_key']],
    ];

    /** @var list<array{level: string, message: string, context: array<mixed>}> */
    private array $records = [];

    private MetadataTemplateResolver $resolver;

    protected function setUp(): void
    {
        $this->records = [];
        $records = &$this->records;
        $logger = new class ($records) extends AbstractLogger {
            /** @param list<array{level: string, message: string, context: array<mixed>}> $records */
            public function __construct(private array &$records)
            {
            }

            /** @inheritDoc */
            public function log($level, $message, array $context = []): void
            {
                $this->records[] = ['level' => (string) $level, 'message' => (string) $message, 'context' => $context];
            }
        };

        $this->resolver = new MetadataTemplateResolver(self::EXTENSIONS, $logger);
    }

    // Scenario: Metadata is rendered with the template of the extension that declares its key
    public function testItResolvesTheTemplateOfTheExtensionThatDeclaresTheKey(): void
    {
        $loader = $this->loaderWithTemplate('@my-extension/metadata/my_key.html.twig');

        $template = $this->resolver->resolve($this->metadata('my_key'), 'Foo', $loader);

        $this->assertSame('@my-extension/metadata/my_key.html.twig', $template);
        $this->assertSame([], $this->records);
    }

    // Scenario: Metadata of an undeclared key is not rendered
    public function testItSkipsMetadataOfAnUndeclaredKeyAndLogsAWarning(): void
    {
        $template = $this->resolver->resolve($this->metadata('unknown_key'), 'Foo', $this->loaderWithTemplate());

        $this->assertNull($template);
        $this->assertCount(1, $this->records);
        $this->assertSame(LogLevel::WARNING, $this->records[0]['level']);
        $this->assertStringContainsString('unknown_key', $this->records[0]['message']);
    }

    // Scenario: Metadata of a declared key without a template is not rendered
    public function testItSkipsMetadataOfADeclaredKeyWithoutATemplateAndLogsAWarning(): void
    {
        $template = $this->resolver->resolve($this->metadata('my_key'), 'Foo', $this->loaderWithTemplate());

        $this->assertNull($template);
        $this->assertCount(1, $this->records);
        $this->assertSame(LogLevel::WARNING, $this->records[0]['level']);
        $this->assertStringContainsString('my_key', $this->records[0]['message']);
        $this->assertStringContainsString('@my-extension/metadata/my_key.html.twig', $this->records[0]['message']);
    }

    // Scenario: A warning is logged for every occurrence of metadata that cannot be rendered
    public function testItLogsAWarningForEveryOccurrence(): void
    {
        $loader = $this->loaderWithTemplate();

        $this->resolver->resolve($this->metadata('unknown_key'), 'Foo', $loader);
        $this->resolver->resolve($this->metadata('unknown_key'), 'Bar', $loader);

        $this->assertCount(2, $this->records);
        $this->assertStringContainsString('Foo', $this->records[0]['message']);
        $this->assertStringContainsString('Bar', $this->records[1]['message']);
    }

    // Scenario: Each extension provides the entry in "On this page" for its own key
    public function testItResolvesTheOnThisPageTemplateOfTheExtensionThatDeclaresEachKey(): void
    {
        $loader = new ArrayLoader([
            '@my-extension/on-this-page/my_key.html.twig' => '',
            '@other-extension/on-this-page/other_key.html.twig' => '',
        ]);

        $this->assertSame(
            '@my-extension/on-this-page/my_key.html.twig',
            $this->resolver->resolveOnThisPage($this->metadata('my_key'), $loader),
        );
        $this->assertSame(
            '@other-extension/on-this-page/other_key.html.twig',
            $this->resolver->resolveOnThisPage($this->metadata('other_key'), $loader),
        );
        $this->assertSame([], $this->records);
    }

    // Scenario: Metadata without a template for "On this page" has no entry there
    public function testItResolvesNoOnThisPageTemplateWhenTheExtensionHasNoneAndLogsNothing(): void
    {
        $loader = $this->loaderWithTemplate('@my-extension/metadata/my_key.html.twig');

        $this->assertNull($this->resolver->resolveOnThisPage($this->metadata('my_key'), $loader));
        $this->assertSame([], $this->records);
    }

    // Scenario: Metadata of an undeclared key has no entry in "On this page"
    public function testItResolvesNoOnThisPageTemplateForAnUndeclaredKey(): void
    {
        $loader = $this->loaderWithTemplate('@my-extension/on-this-page/unknown_key.html.twig');

        $this->assertNull($this->resolver->resolveOnThisPage($this->metadata('unknown_key'), $loader));
        $this->assertSame([], $this->records);
    }

    private function loaderWithTemplate(string|null $name = null): LoaderInterface
    {
        return new ArrayLoader($name === null ? [] : [$name => '']);
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
