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

use ArrayIterator;
use InvalidArgumentException;
use League\CommonMark\ConverterInterface;
use League\Uri\Uri;
use phpDocumentor\Descriptor\AttributeDescriptor;
use phpDocumentor\Descriptor\Collection;
use phpDocumentor\Descriptor\Descriptor;
use phpDocumentor\Descriptor\DescriptorAbstract;
use phpDocumentor\Descriptor\DocBlock\DescriptionDescriptor;
use phpDocumentor\Descriptor\DocumentationSetDescriptor;
use phpDocumentor\Descriptor\EnumDescriptor;
use phpDocumentor\Descriptor\Interfaces\ArgumentInterface;
use phpDocumentor\Descriptor\Interfaces\AttributedInterface;
use phpDocumentor\Descriptor\Interfaces\AttributeInterface;
use phpDocumentor\Descriptor\Interfaces\ClassInterface;
use phpDocumentor\Descriptor\Interfaces\ConstantInterface;
use phpDocumentor\Descriptor\Interfaces\ContainerInterface;
use phpDocumentor\Descriptor\Interfaces\EnumCaseInterface;
use phpDocumentor\Descriptor\Interfaces\EnumInterface;
use phpDocumentor\Descriptor\Interfaces\FileInterface;
use phpDocumentor\Descriptor\Interfaces\FunctionInterface;
use phpDocumentor\Descriptor\Interfaces\InterfaceInterface;
use phpDocumentor\Descriptor\Interfaces\MethodInterface;
use phpDocumentor\Descriptor\Interfaces\NamespaceInterface;
use phpDocumentor\Descriptor\Interfaces\PackageInterface;
use phpDocumentor\Descriptor\Interfaces\PropertyInterface;
use phpDocumentor\Descriptor\Interfaces\TraitInterface;
use phpDocumentor\Descriptor\Interfaces\VisibilityInterface;
use phpDocumentor\Descriptor\NamespaceDescriptor;
use phpDocumentor\Descriptor\PackageDescriptor;
use phpDocumentor\Descriptor\ProjectDescriptor;
use phpDocumentor\Descriptor\TableOfContents\Entry;
use phpDocumentor\Descriptor\Tag\ExampleDescriptor;
use phpDocumentor\Descriptor\Tag\LinkDescriptor;
use phpDocumentor\Descriptor\Tag\SeeDescriptor;
use phpDocumentor\Path;
use phpDocumentor\Reflection\DocBlock\Tags\Reference;
use phpDocumentor\Reflection\Fqsen;
use phpDocumentor\Reflection\Php\Expression;
use phpDocumentor\Reflection\Type;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Extension\GlobalsInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;
use Webmozart\Assert\Assert;

use function array_map;
use function array_unshift;
use function in_array;
use function ltrim;
use function method_exists;
use function sprintf;
use function str_replace;
use function strtolower;
use function var_export;
use function vsprintf;

/**
 * Basic extension adding phpDocumentor specific functionality for Twig
 * templates.
 *
 * Global variables:
 *
 * - *ast_node*, the current $data element
 *
 * Functions:
 *
 * - *path(string) *, converts the given relative path to be based of the projects
 *   root instead of the current directory
 *
 * Filters:
 *
 * - *markdown*, converts the associated text from Markdown formatting to HTML.
 * - *trans*, translates the given string
 * - *route*, attempts to generate a URL for a given Descriptor
 * - *sort_desc*, sorts the given objects by their Name property/getter in a descending fashion
 * - *sort_asc*, sorts the given objects by their Name property/getter in a ascending fashion
 */
final class Extension extends AbstractExtension implements ExtensionInterface, GlobalsInterface
{
    /**
     * Registers the structure and transformation with this extension.
     *
     * @param ProjectDescriptor $project Represents the complete Abstract Syntax Tree.
     */
    public function __construct(
        private readonly ProjectDescriptor $project,
        private readonly DocumentationSetDescriptor $documentationSet,
        private readonly ConverterInterface $markdownConverter,
        private LinkRenderer $routeRenderer,
        private readonly RelativePathToRootConverter $relativePathToRootConverter,
        private readonly PathBuilder $pathBuilder,
    ) {
        $this->routeRenderer = $this->routeRenderer->withProject($project)->forDocumentationSet($documentationSet);
    }

    /**
     * Initialize series of globals used by the writers to set the context
     *
     * @return array{
     *     project: ProjectDescriptor,
     *     documentationSet: DocumentationSetDescriptor,
     *     node: ?Descriptor,
     *     usesNamespaces: bool,
     *     usesPackages: bool,
     *     destinationPath: ?string,
     *     parameter: array<string, mixed>,
     *     env: mixed
     * }
     */
    public function getGlobals(): array
    {
        return [
            'project' => $this->project,
            'documentationSet' => $this->documentationSet,
            'node' => null,
            'usesNamespaces' => true,
            'usesPackages' => true,
            'destinationPath' => null,
            'parameter' => [],
            'env' => null,
        ];
    }

    /** @return list<TwigTest> */
    public function getTests(): array
    {
        return [
            new TwigTest('element container', static fn (Descriptor $el) => $el instanceof ContainerInterface),
            new TwigTest('namespace', static fn (Descriptor $el) => $el instanceof NamespaceInterface),
            new TwigTest('package', static fn (Descriptor $el) => $el instanceof PackageInterface),
            new TwigTest('file', static fn (Descriptor $el) => $el instanceof FileInterface),
            new TwigTest('class', static fn (Descriptor $el) => $el instanceof ClassInterface),
            new TwigTest('interface', static fn (Descriptor $el) => $el instanceof InterfaceInterface),
            new TwigTest('enum', static fn (Descriptor $el) => $el instanceof EnumInterface),
            new TwigTest('enumCase', static fn (Descriptor $el) => $el instanceof EnumCaseInterface),
            new TwigTest('trait', static fn (Descriptor $el) => $el instanceof TraitInterface),
            new TwigTest('property', static fn (Descriptor $el) => $el instanceof PropertyInterface),
            new TwigTest('method', static fn (Descriptor $el) => $el instanceof MethodInterface),
            new TwigTest('argument', static fn (Descriptor $el) => $el instanceof ArgumentInterface),
            new TwigTest('attribute', static fn (Descriptor $el) => $el instanceof AttributeInterface),
            new TwigTest('attributed', static fn (Descriptor $el) => $el instanceof AttributedInterface),
            new TwigTest('function', static fn (Descriptor $el) => $el instanceof FunctionInterface),
            new TwigTest('constant', static fn (Descriptor $el) => $el instanceof ConstantInterface),
        ];
    }

    /**
     * Returns a listing of all functions that this extension adds.
     *
     * This method is automatically used by Twig upon registering this
     * extension (which is done automatically by phpDocumentor) to determine
     * an additional list of functions.
     *
     * See the Class' DocBlock for a listing of functionality added by this
     * Extension.
     *
     * @return TwigFunction[]
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction(
                'renderBaseUrlHeader',
                function (array $context): string {
                    $this->routeRenderer = $this->contextRouteRenderer($context);
                    $absolutePath = $this->relativePathToRootConverter->convert(
                        $this->routeRenderer->getDestination(),
                        '/',
                    );
                    if (! $absolutePath) {
                        return '<base href="./">';
                    }

                    return '<base href="' . $absolutePath . '">';
                },
                ['is_safe' => ['all'], 'needs_context' => true],
            ),
            new TwigFunction(
                'path',
                function (string $url): string {
                    $path = $this->relativePathToRootConverter->convert('', $url);

                    Assert::notNull($path);

                    return $path;
                },
            ),
            new TwigFunction(
                'link',
                function (object $element): string {
                    if (
                        ! $element instanceof Fqsen
                        && ! $element instanceof Uri
                        && ! $element instanceof Descriptor
                    ) {
                        return '';
                    }

                    return $this->pathBuilder->link($element);
                },
            ),
            new TwigFunction(
                'breadcrumbs',
                static function (DescriptorAbstract $baseNode): array {
                    $results   = [];
                    $namespace = $baseNode instanceof NamespaceDescriptor
                        ? $baseNode->getParent()
                        : $baseNode->getNamespace();
                    while ($namespace instanceof NamespaceDescriptor && $namespace->getName() !== '\\') {
                        array_unshift($results, $namespace);
                        $namespace = $namespace->getParent();
                    }

                    return $results;
                },
            ),
            new TwigFunction(
                'packages',
                static function (DescriptorAbstract $baseNode): array {
                    $results = [];
                    $package = $baseNode instanceof PackageDescriptor
                        ? $baseNode->getParent()
                        : $baseNode->getPackage();
                    while ($package instanceof PackageDescriptor && $package->getName() !== '\\') {
                        array_unshift($results, $package);
                        $package = $package->getParent();
                    }

                    return $results;
                },
            ),
            new TwigFunction(
                'methods',
                static function (DescriptorAbstract $descriptor): Collection {
                    $methods = new Collection();
                    if (method_exists($descriptor, 'getMethods')) {
                        $methods = $methods->merge($descriptor->getMethods());
                    }

                    if (method_exists($descriptor, 'getMagicMethods')) {
                        $methods = $methods->merge($descriptor->getMagicMethods());
                    }

                    if (method_exists($descriptor, 'getInheritedMethods')) {
                        $methods = $methods->merge($descriptor->getInheritedMethods());
                    }

                    return $methods;
                },
            ),
            new TwigFunction(
                'properties',
                static function (DescriptorAbstract $descriptor): Collection {
                    $properties = new Collection();
                    if (method_exists($descriptor, 'getProperties')) {
                        $properties = $properties->merge($descriptor->getProperties());
                    }

                    if (method_exists($descriptor, 'getMagicProperties')) {
                        $properties = $properties->merge($descriptor->getMagicProperties());
                    }

                    if (method_exists($descriptor, 'getInheritedProperties')) {
                        $properties = $properties->merge($descriptor->getInheritedProperties());
                    }

                    return $properties;
                },
            ),
            new TwigFunction(
                'constants',
                static function (DescriptorAbstract $descriptor): Collection {
                    $constants = new Collection();
                    if (method_exists($descriptor, 'getConstants')) {
                        $constants = $constants->merge($descriptor->getConstants());
                    }

                    if (method_exists($descriptor, 'getMagicConstants')) {
                        $constants = $constants->merge($descriptor->getMagicConstants());
                    }

                    if (method_exists($descriptor, 'getInheritedConstants')) {
                        $constants = $constants->merge($descriptor->getInheritedConstants());
                    }

                    return $constants;
                },
            ),
            new TwigFunction(
                'cases',
                static function (DescriptorAbstract $descriptor): Collection {
                    if ($descriptor instanceof EnumDescriptor) {
                        return $descriptor->getCases();
                    }

                    return new Collection();
                },
            ),
            new TwigFunction(
                'attributes',
                static function (AttributedInterface $descriptor): Collection {
                    $attributes = Collection::fromInterfaceString(AttributeInterface::class);
                    if (method_exists($descriptor, 'getAttributes')) {
                        $attributes = $attributes->merge($descriptor->getAttributes());
                    }

                    return $attributes;
                },
            ),
            new TwigFunction(
                'toc',
                static function (
                    Environment $env,
                    Entry $entry,
                    string $template,
                    int|null $maxDepth = null,
                    int $depth = 0,
                ): string {
                    if ($maxDepth === $depth) {
                        return '';
                    }

                    return $env->render(
                        $template,
                        [
                            'entry' => $entry,
                            'depth' => ++$depth,
                            'maxDepth' => $maxDepth,
                        ],
                    );
                },
                [
                    'needs_environment' => true,
                    'is_safe' => ['html'],
                ],
            ),
        ];
    }

    /**
     * Returns a list of all filters that are exposed by this extension.
     *
     * @return TwigFilter[]
     */
    public function getFilters(): array
    {
        return [
            'markdown' => new TwigFilter(
                'markdown',
                fn (string|null $value): string => str_replace(
                    ['<pre>', '<code>'],
                    ['<pre class="prettyprint">', '<code class="prettyprint">'],
                    $this->markdownConverter->convert($value ?? '')->getContent(),
                ),
                ['is_safe' => ['all']],
            ),
            'trans' => new TwigFilter(
                'trans',
                static fn ($value) => $value,
            ),
            'route' => new TwigFilter(
                'route',
                fn (
                    $value,
                    string $presentation = LinkRenderer::PRESENTATION_NORMAL,
                ) => $this->routeRenderer->render($value, $presentation),
                ['is_safe' => ['all']],
            ),
            'sort' => new TwigFilter('sort_*', $this->sort(...)),
            'sortByVisibility' => new TwigFilter('sortByVisibility', $this->sortByVisibility(...)),
            'export' => new TwigFilter(
                'export',
                static fn ($var) => var_export($var, true),
            ),
            'description' => new TwigFilter(
                'description',
                $this->renderDescription(...),
                ['needs_context' => true],
            ),
            'expression' => new TwigFilter(
                'expression',
                $this->renderExpression(...),
                ['needs_context' => true, 'is_safe' => ['html']],
            ),
            'shortFQSEN' => new TwigFilter(
                'shortFQSEN',
                static function (string $fqsenOrTitle) {
                    try {
                        return (new Fqsen($fqsenOrTitle))->getName();
                    } catch (InvalidArgumentException) {
                    }

                    return $fqsenOrTitle;
                },
            ),
            'specializedAttributes' => new TwigFilter(
                'specializedAttributes',
                static function (Collection $attributes): Collection {
                    $filtered = Collection::fromClassString(AttributeDescriptor::class);
                    foreach ($attributes as $attribute) {
                        if (in_array((string) $attribute->getFullyQualifiedStructuralElementName(), ['\Deprecated'])) {
                            continue;
                        }

                        $filtered->add($attribute);
                    }

                    return $filtered;
                },
            ),
        ];
    }

    /** @param mixed[] $context */
    public function renderDescription(array $context, DescriptionDescriptor|null $description): string
    {
        if ($description === null || $description->getBodyTemplate() === '') {
            return '';
        }

        $tagStrings = [];
        foreach ($description->getTags() as $tag) {
            if ($tag instanceof SeeDescriptor) {
                $presentation = LinkRenderer::PRESENTATION_CLASS_SHORT;
                if ($tag->getDescription()->isEmpty() === false) {
                    $presentation = $this->renderDescription($context, $tag->getDescription());
                }

                $tagStrings[] = $this->renderRoute($context, $tag->getReference(), $presentation);
            } elseif ($tag instanceof LinkDescriptor) {
                $text = $this->renderDescription($context, $tag->getDescription());
                $tagStrings[] = sprintf('[%s](%s)', $text, $tag->getLink());
            } elseif ($tag instanceof ExampleDescriptor) {
                $tagStrings[] = $tag->getDescription() . "\n"
                    . '```php' . "\n" . $tag->getExample() . "\n" . '```';
            } else {
                $tagStrings[] = (string) $tag;
            }
        }

        return vsprintf($description->getBodyTemplate(), $tagStrings);
    }

    /** @param mixed[] $context */
    public function renderExpression(array $context, Expression|null $expression): string
    {
        if ($expression === null) {
            return '';
        }

        $parts = array_map(
            fn (Fqsen|Type $value) => $this->renderRoute($context, $value, LinkRenderer::PRESENTATION_CLASS_SHORT),
            $expression->getParts(),
        );

        return $expression->render($parts);
    }

    /**
     * @param mixed[] $context
     * @param array<Type>|Type|DescriptorAbstract|Fqsen|Reference\Reference|Path|string|iterable<mixed> $value
     *
     * @return string[]|string
     */
    public function renderRoute(array $context, $value, string $presentation)
    {
        $routeRenderer = $this->contextRouteRenderer($context);

        return $routeRenderer->render($value, $presentation);
    }

    /** @param mixed[] $context */
    private function contextRouteRenderer(array $context): LinkRenderer
    {
        return $this->routeRenderer
            ->withDestination(ltrim($context['destinationPath'], '/\\'))
            ->withProject($context['project'])
            ->forDocumentationSet($context['documentationSet']);
    }

    /**
     * @param Collection<Descriptor> $collection
     *
     * @return ArrayIterator<array-key, Descriptor>
     */
    public function sort(string $direction, Collection $collection): ArrayIterator
    {
        $iterator = $collection->getIterator();
        $iterator->uasort(
            static function (Descriptor $a, Descriptor $b) use ($direction) {
                $aElem = strtolower($a->getName());
                $bElem = strtolower($b->getName());
                if ($aElem === $bElem) {
                    return 0;
                }

                if (
                    ($direction === 'asc' && $aElem > $bElem) ||
                    ($direction === 'desc' && $aElem < $bElem)
                ) {
                    return 1;
                }

                return -1;
            },
        );

        return $iterator;
    }

    /**
     * @param Collection<Descriptor> $collection
     *
     * @return ArrayIterator<array-key, Descriptor>
     */
    public function sortByVisibility(Collection $collection): ArrayIterator
    {
        $iterator = $collection->getIterator();
        $iterator->uasort(
            static function (Descriptor $a, Descriptor $b): int {
                $prio = 0;
                if ($a instanceof VisibilityInterface && $b instanceof VisibilityInterface) {
                    $visibilityPriorityA = $a->getVisibility()->readModifier()->getWeight();
                    $visibilityPriorityB = $b->getVisibility()->readModifier()->getWeight();
                    $prio = $visibilityPriorityA <=> $visibilityPriorityB;
                }

                if ($prio !== 0) {
                    return $prio;
                }

                $aElem = strtolower($a->getName());
                $bElem = strtolower($b->getName());

                return $aElem <=> $bElem;
            },
        );

        return $iterator;
    }
}
